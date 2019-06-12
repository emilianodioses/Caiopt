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

        //Calculo los totales de las ventas por tipos de pago
        $total_recibos = 0;
        $recibos = $em->getRepository('AppBundle:Recibo')->findBy(Array('activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_id));
        foreach($recibos as $recibo) {
            $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo' => $recibo->getId(), 'activo'=> '1'));

            foreach($clientePagos as $clientePago) {
                $cajaDetalles[$clientePago->getPagoTipo()->getId()]['total'] += $clientePago->getImporte();
                
                $total_recibos += $clientePago->getImporte();
            }
        }

        //Calculo los porcentajes de cada condicion de venta, siempre que las haya
        if ($total_recibos > 0) {
            foreach($pagoTipos as $pagoTipo) {
                $cajaDetalles[$pagoTipo->getId()]['porcentaje'] = number_format($cajaDetalles[$pagoTipo->getId()]['total'] * 100 / $total_recibos, 2);
            }
        }

        $comprobantesVentas = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta', 'activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_id));

        $comprobantesCompras = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'compra', 'activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_id));

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursal($sucursal_id);

        $ordenesTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->findAll_sucursal($sucursal_id);

        $ordenesTrabajoHoy = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursalFecha($sucursal_id, $fecha_now);
        
        return $this->render('dashboard/index.html.twig', array(
            'cantidadVentas' => count($comprobantesVentas),
            'totalRecibos' => $total_recibos,
            'cantidadCompras' => count($comprobantesCompras),
            'cantidadOrdenesTrabajo' => count($ordenesTrabajoHoy),
            'cajaDetalles' => $cajaDetalles,
            'ordenesTrabajo' => $ordenesTrabajo,
            'ordenesTrabajoContactologia' => $ordenesTrabajoContactologia
        ));
    }
}
