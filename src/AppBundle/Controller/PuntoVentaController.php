<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PuntoVenta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Puntoventum controller.
 *
 */
class PuntoVentaController extends Controller
{
    /**
     * Lists all puntoVentum entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PuntoVenta', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:PuntoVenta')->findByTexto($texto);


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('puntoventa/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new puntoVentum entity.
     *
     */
    public function newAction(Request $request)
    {
        $puntoVenta = new PuntoVenta();
        $form = $this->createForm('AppBundle\Form\PuntoVentaType', $puntoVenta);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $puntoVenta->setActivo(true);
            $puntoVenta->setCreatedBy($this->getUser());
            $puntoVenta->setCreatedAt(new \DateTime("now"));
            $puntoVenta->setUpdatedBy($this->getUser());
            $puntoVenta->setUpdatedAt(new \DateTime("now"));
            
            $em->persist($puntoVenta);
            $em->flush();

            return $this->redirectToRoute('puntoventa_show', array('id' => $puntoVenta->getId()));
        }

        return $this->render('puntoventa/new.html.twig', array(
            'puntoVenta' => $puntoVenta,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a puntoVentum entity.
     *
     */
    public function showAction(PuntoVenta $puntoVenta)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PuntoVenta', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $deleteForm = $this->createDeleteForm($puntoVenta);

        return $this->render('puntoventa/show.html.twig', array(
            'puntoVenta' => $puntoVenta,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing puntoVentum entity.
     *
     */
    public function editAction(Request $request, PuntoVenta $puntoVenta)
    {
        $deleteForm = $this->createDeleteForm($puntoVenta);
        $editForm = $this->createForm('AppBundle\Form\PuntoVentaType', $puntoVenta);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('puntoventa_show', array('id' => $puntoVenta->getId()));
        }

        return $this->render('puntoventa/edit.html.twig', array(
            'puntoVenta' => $puntoVenta,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a puntoVentum entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $puntoVenta = $em->getRepository('AppBundle:PuntoVenta')->find($id);

        if ($puntoVenta->getActivo() > 0)
            $puntoVenta->setActivo(0);
        else
            $puntoVenta->setActivo(1); 
        
        $puntoVenta->setUpdatedBy($this->getUser()); 
        $puntoVenta->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('puntoventa_index');
    }

    /**
     * Creates a form to delete a puntoVentum entity.
     *
     * @param PuntoVenta $puntoVentum The puntoVentum entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PuntoVenta $puntoVentum)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('puntoventa_delete', array('id' => $puntoVentum->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
