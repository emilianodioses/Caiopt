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

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();

        $afip_condiciones_venta = $em->getRepository('AppBundle:AfipCondicionVenta')->findBy(Array('activo'=> '1'), array('descripcion' => 'ASC'));

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findAll_sucursal($this->getUser()->getSucursal()->getId());
        
        return $this->render('dashboard/index.html.twig', array(
            'afip_condiciones_venta' => $afip_condiciones_venta,
            'ordenesTrabajo' => $ordenesTrabajo
        ));
    }
}
