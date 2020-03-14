<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MovimientoInterno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\LibroCajaDetalle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Movimientointerno controller.
 *
 */
class MovimientoInternoController extends Controller
{
    /**
     * Lists all movimientoInterno entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('MovimientoInterno', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:MovimientoInterno')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('movimientointerno/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new movimientoInterno entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('MovimientoInterno', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $movimiento = new MovimientoInterno();
        $movimiento->setFecha(new \DateTime("now"));
        $movimiento->setSucursalOrigen($this->getUser()->getSucursal());
        $form = $this->createForm('AppBundle\Form\MovimientoInternoType', $movimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //$fecha_now = new \DateTime("now");
            $libroCajaOrigen = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $movimiento->getFecha(), 'sucursal' => $movimiento->getSucursalOrigen(), 'activo' => 1));
            $libroCajaDestino = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $movimiento->getFecha(), 'sucursal' => $movimiento->getSucursalDestino(), 'activo' => 1));

            if (is_null($libroCajaOrigen)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja para la sucursal de Origen. Debe generar uno antes de cargar movimientos internos.');

                return $this->redirectToRoute('movimientointerno_new', array('request' => $request, 'movimiento' => $movimiento->getId()));
            }

            if (is_null($libroCajaDestino)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja para la sucursal de Destino. Debe generar uno antes de cargar movimientos internos.');

                return $this->redirectToRoute('movimientointerno_new', array('request' => $request, 'movimiento' => $movimiento->getId()));
            }
           
            $movimiento->setActivo(true);
            $movimiento->setCreatedBy($this->getUser());
            $movimiento->setCreatedAt(new \DateTime("now"));
            $movimiento->setUpdatedBy($this->getUser());
            $movimiento->setUpdatedAt(new \DateTime("now"));

            $em->persist($movimiento);
            $em->flush();

            // Libro de caja Sucursal Origen
            $libroCajaOrigenDetalle = new Librocajadetalle();
            $libroCajaOrigenDetalle->setLibroCaja($libroCajaOrigen);
            $libroCajaOrigenDetalle->setPagoTipo($movimiento->getPagoTipo());
            $libroCajaOrigenDetalle->setOrigen('Movimiento Interno');
            $libroCajaOrigenDetalle->setTipo('Egreso de Caja');
            $libroCajaOrigenDetalle->setDescripcion($movimiento->getId());
            $libroCajaOrigenDetalle->setImporte($movimiento->getMonto());
            $libroCajaOrigenDetalle->setMovimientoCategoria($movimiento->getMovimientoCategoria());
            $libroCajaOrigenDetalle->setMovimientoInterno($movimiento);
            $libroCajaOrigenDetalle->setActivo(true);
            $libroCajaOrigenDetalle->setCreatedBy($this->getUser()->getId());
            $libroCajaOrigenDetalle->setCreatedAt(new \DateTime("now"));
            $libroCajaOrigenDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaOrigenDetalle->setUpdatedAt(new \DateTime("now"));

            if ($libroCajaOrigenDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $saldo = $libroCajaOrigen->getSaldoFinal();
                $saldo -= $libroCajaOrigenDetalle->getImporte();
                $libroCajaOrigen->setSaldoFinal($saldo);
            }

            // Libro de caja Sucursal Destino
            $libroCajaDestinoDetalle = new Librocajadetalle();
            $libroCajaDestinoDetalle->setLibroCaja($libroCajaDestino);
            $libroCajaDestinoDetalle->setPagoTipo($movimiento->getPagoTipo());
            $libroCajaDestinoDetalle->setOrigen('Movimiento Interno');
            $libroCajaDestinoDetalle->setTipo('Ingreso a Caja');
            $libroCajaDestinoDetalle->setDescripcion($movimiento->getId());
            $libroCajaDestinoDetalle->setImporte($movimiento->getMonto());
            $libroCajaDestinoDetalle->setMovimientoCategoria($movimiento->getMovimientoCategoria());
            $libroCajaDestinoDetalle->setMovimientoInterno($movimiento);
            $libroCajaDestinoDetalle->setActivo(true);
            $libroCajaDestinoDetalle->setCreatedBy($this->getUser()->getId());
            $libroCajaDestinoDetalle->setCreatedAt(new \DateTime("now"));
            $libroCajaDestinoDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDestinoDetalle->setUpdatedAt(new \DateTime("now"));

            if ($libroCajaDestinoDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $saldo = $libroCajaDestino->getSaldoFinal();
                $saldo += $libroCajaDestinoDetalle->getImporte();
                $libroCajaDestino->setSaldoFinal($saldo);
            }

            $em->persist($libroCajaOrigenDetalle);
            $em->persist($libroCajaDestinoDetalle);

            $em->flush();

            return $this->redirectToRoute('movimientointerno_show', array('id' => $movimiento->getId()));
        }

        return $this->render('movimientointerno/new.html.twig', array(
            'movimiento' => $movimiento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a movimientoInterno entity.
     *
     */
    public function showAction(MovimientoInterno $movimiento)
    {
        $deleteForm = $this->createDeleteForm($movimiento);

        return $this->render('movimientointerno/show.html.twig', array(
            'movimiento' => $movimiento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing movimientoInterno entity.
     *
     */
    public function editAction(Request $request, MovimientoInterno $movimientoInterno)
    {
        $deleteForm = $this->createDeleteForm($movimientoInterno);
        $editForm = $this->createForm('AppBundle\Form\MovimientoInternoType', $movimientoInterno);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movimientointerno_edit', array('id' => $movimientoInterno->getId()));
        }

        return $this->render('movimientointerno/edit.html.twig', array(
            'movimientoInterno' => $movimientoInterno,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a movimientoInterno entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $movimiento = $em->getRepository('AppBundle:MovimientoInterno')->find($id);

        $libroCajaDetalles= $em->getRepository('AppBundle:LibroCajaDetalle')->findBy(array('movimientoInterno'=> $movimiento->getId()));

        foreach($libroCajaDetalles as $libroCajaDetalle) {
            $libroCajaDetalle->setActivo(false);
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId()); 
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now")); 

            $libroCaja = $libroCajaDetalle->getLibroCaja();
            $saldo = $libroCaja->getSaldoFinal();
            
            if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                $saldo -= $libroCajaDetalle->getImporte();
            }
            else {
                $saldo += $libroCajaDetalle->getImporte();
            }    
            
            if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $libroCaja->setSaldoFinal($saldo);
            }
            
            $em->flush();
        }

        if ($movimiento->getActivo() > 0)
            $movimiento->setActivo(0);
        else
            $movimiento->setActivo(1); 
        
        $movimiento->setUpdatedBy($this->getUser()); 
        $movimiento->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('movimientointerno_index');
    }

    /**
     * Creates a form to delete a movimientoInterno entity.
     *
     * @param MovimientoInterno $movimientoInterno The movimientoInterno entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MovimientoInterno $movimientoInterno)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movimientointerno_delete', array('id' => $movimientoInterno->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
