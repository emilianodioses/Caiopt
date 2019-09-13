<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DashboardController extends Controller
{
	/**
     * Lists all usuario entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Dashboard', 'Index', $this->getUser()->getRol())):
            //return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
            return $this->render('dashboard/index_not_authorized.html.twig');
        endif;

        $em = $this->getDoctrine()->getManager();
        $sucursal_id = $this->getUser()->getSucursal()->getId();

        $fecha_now = new \DateTime("now");
        $fecha_now->setTime(00, 00, 00);

        $pagoTipos = $em->getRepository('AppBundle:PagoTipo')->findBy(Array('activo'=> '1'), array('nombre' => 'ASC'));

        //inicializo el arreglo de los tipos de pago
        $cajaDetalles = array();
        foreach($pagoTipos as $pagoTipo) {
            $cajaDetalles[$pagoTipo->getId()]['descripcion'] = $pagoTipo->getNombre();
            $cajaDetalles[$pagoTipo->getId()]['total'] = 0;
            $cajaDetalles[$pagoTipo->getId()]['porcentaje'] = 0;
        }

        $libroCaja = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $fecha_now, 'sucursal' => $sucursal_id, 'activo' => 1));

        //Calculo los totales de ingresos por tipos de pago
        $total_caja = 0;
        $libroCajaDetalles = $em->getRepository('AppBundle:LibroCajaDetalle')->findBy(Array('libroCaja' => $libroCaja, 'activo' => 1));
        foreach($libroCajaDetalles as $libroCajaDetalle) {
            if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                $cajaDetalles[$libroCajaDetalle->getPagoTipo()->getId()]['total'] += $libroCajaDetalle->getImporte();
                
                $total_caja += $libroCajaDetalle->getImporte();
            }
            else {
                $cajaDetalles[$libroCajaDetalle->getPagoTipo()->getId()]['total'] -= $libroCajaDetalle->getImporte();
                
                $total_caja -= $libroCajaDetalle->getImporte();
            }
        }

        //Calculo los porcentajes de cada condicion de venta, siempre que las haya
        if ($total_caja > 0) {
            foreach($pagoTipos as $pagoTipo) {
                $cajaDetalles[$pagoTipo->getId()]['porcentaje'] = number_format($cajaDetalles[$pagoTipo->getId()]['total'] * 100 / $total_caja, 2);
            }
        }

        $comprobantesVentas = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta', 'activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_id));

        $comprobantesCompras = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'compra', 'activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_id));

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursal($sucursal_id);

        $ordenesTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->findAll_sucursal($sucursal_id);

        $ordenesTrabajoHoy = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursalFecha($sucursal_id, $fecha_now);
        
        return $this->render('dashboard/index.html.twig', array(
            'cantidadVentas' => count($comprobantesVentas),
            'totalCaja' => $total_caja,
            'cantidadCompras' => count($comprobantesCompras),
            'cantidadOrdenesTrabajo' => count($ordenesTrabajoHoy),
            'cajaDetalles' => $cajaDetalles,
            'ordenesTrabajo' => $ordenesTrabajo,
            'ordenesTrabajoContactologia' => $ordenesTrabajoContactologia
        ));
    }
}
