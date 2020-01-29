<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MovimientoInterno;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            10/*limit per page*/
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
        $form = $this->createForm('AppBundle\Form\MovimientoInternoType', $movimiento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

           
            $movimiento->setActivo(true);
            $movimiento->setCreatedBy($this->getUser());
            $movimiento->setCreatedAt(new \DateTime("now"));
            $movimiento->setUpdatedBy($this->getUser());
            $movimiento->setUpdatedAt(new \DateTime("now"));

            $em->persist($movimiento);
            $em->flush();

            return $this->redirectToRoute('movimientointerno_show', array('id' => $movimiento->getId()));
        }

        return $this->render('movimientointerno/new.html.twig', array(
            'cliente' => $movimiento,
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
