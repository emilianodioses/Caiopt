<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ComprobanteDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Comprobantedetalle controller.
 *
 */
class ComprobanteDetalleController extends Controller
{
    /**
     * Lists all comprobanteDetalle entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteDetalle', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findAll();

        return $this->render('comprobantedetalle/index.html.twig', array(
            'comprobanteDetalles' => $comprobanteDetalles,
        ));
    }

    /**
     * Creates a new comprobanteDetalle entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteDetalle', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $comprobanteDetalle = new Comprobantedetalle();
        $form = $this->createForm('AppBundle\Form\ComprobanteDetalleType', $comprobanteDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comprobanteDetalle);
            $em->flush();

            return $this->redirectToRoute('comprobantedetalle_show', array('id' => $comprobanteDetalle->getId()));
        }

        return $this->render('comprobantedetalle/new.html.twig', array(
            'comprobanteDetalle' => $comprobanteDetalle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comprobanteDetalle entity.
     *
     */
    public function showAction(ComprobanteDetalle $comprobanteDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteDetalle', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($comprobanteDetalle);

        return $this->render('comprobantedetalle/show.html.twig', array(
            'comprobanteDetalle' => $comprobanteDetalle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comprobanteDetalle entity.
     *
     */
    public function editAction(Request $request, ComprobanteDetalle $comprobanteDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteDetalle', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($comprobanteDetalle);
        $editForm = $this->createForm('AppBundle\Form\ComprobanteDetalleType', $comprobanteDetalle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comprobantedetalle_edit', array('id' => $comprobanteDetalle->getId()));
        }

        return $this->render('comprobantedetalle/edit.html.twig', array(
            'comprobanteDetalle' => $comprobanteDetalle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comprobanteDetalle entity.
     *
     */
    public function deleteAction(Request $request, ComprobanteDetalle $comprobanteDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteDetalle', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $form = $this->createDeleteForm($comprobanteDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comprobanteDetalle);
            $em->flush();
        }

        return $this->redirectToRoute('comprobantedetalle_index');
    }

    /**
     * Creates a form to delete a comprobanteDetalle entity.
     *
     * @param ComprobanteDetalle $comprobanteDetalle The comprobanteDetalle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ComprobanteDetalle $comprobanteDetalle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comprobantedetalle_delete', array('id' => $comprobanteDetalle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
