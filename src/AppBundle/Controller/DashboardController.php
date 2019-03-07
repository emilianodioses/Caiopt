<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
	/**
     * Lists all usuario entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sucursal_Id = $this->getUser()->getSucursal()->getId();

        $fecha_now = new \DateTime("now");
        $fecha_now->setTime(00, 00, 00);

        $afipCondicionesVenta = $em->getRepository('AppBundle:AfipCondicionVenta')->findBy(Array('activo'=> '1'), array('descripcion' => 'ASC'));

        //Calculo los totales de las ventas por condicion de venta
        $cajaDetalles = array();
        $total_ventas = 0;
        $cantidad_ventas = 0;
        foreach($afipCondicionesVenta as $acv) {
            $comprobantesVentas = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta', 'activo'=> '1', 'condicionVenta' => $acv->getId(), 'fecha' => $fecha_now, 'sucursal' => $sucursal_Id));

            $total_ventas_acv = 0;
            foreach($comprobantesVentas as $cv) {
                $total_ventas_acv += $cv->getTotal();
                
                $total_ventas += $cv->getTotal();
                $cantidad_ventas++;
            }

            $cajaDetalles[$acv->getId()]['descripcion'] = $acv->getDescripcion();
            $cajaDetalles[$acv->getId()]['total'] = $total_ventas_acv;
            $cajaDetalles[$acv->getId()]['porcentaje'] = 0;
        }

        //Calculo los porcentajes de cada condicion de venta, siempre que las haya
        if ($total_ventas > 0) {
            foreach($afipCondicionesVenta as $acv) {
                $cajaDetalles[$acv->getId()]['porcentaje'] = number_format($cajaDetalles[$acv->getId()]['total'] * 100 / $total_ventas, 2);
            }
        }

        $comprobantesCompras = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'compra', 'activo'=> '1', 'fecha' => $fecha_now, 'sucursal' => $sucursal_Id));

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursal($sucursal_Id);

        $ordenesTrabajoHoy = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursalFecha($sucursal_Id, $fecha_now);
        
        return $this->render('dashboard/index.html.twig', array(
            'cantidadVentas' => $cantidad_ventas,
            'totalVentas' => $total_ventas,
            'cantidadCompras' => count($comprobantesCompras),
            'cantidadOrdenesTrabajo' => count($ordenesTrabajoHoy),
            'cajaDetalles' => $cajaDetalles,
            'ordenesTrabajo' => $ordenesTrabajo
        ));
    }
}
