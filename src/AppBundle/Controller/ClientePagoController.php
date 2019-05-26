<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ClientePago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clientepago controller.
 *
 */
class ClientePagoController extends Controller
{
    /**
     * Lists all clientePago entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ClientePago', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findAll();

        return $this->render('clientepago/index.html.twig', array(
            'clientePagos' => $clientePagos,
        ));
    }

    /**
     * Creates a new clientePago entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ClientePago', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $clientePago = new Clientepago();
        $form = $this->createForm('AppBundle\Form\ClientePagoType', $clientePago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($clientePago);
            $em->flush();

            return $this->redirectToRoute('clientepago_show', array('id' => $clientePago->getId()));
        }

        return $this->render('clientepago/new.html.twig', array(
            'clientePago' => $clientePago,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a clientePago entity.
     *
     */
    public function showAction(ClientePago $clientePago)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ClientePago', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($clientePago);

        return $this->render('clientepago/show.html.twig', array(
            'clientePago' => $clientePago,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing clientePago entity.
     *
     */
    public function editAction(Request $request, ClientePago $clientePago)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ClientePago', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($clientePago);
        $editForm = $this->createForm('AppBundle\Form\ClientePagoType', $clientePago);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('clientepago_edit', array('id' => $clientePago->getId()));
        }

        return $this->render('clientepago/edit.html.twig', array(
            'clientePago' => $clientePago,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a clientePago entity.
     *
     */
    public function deleteAction(Request $request, ClientePago $clientePago)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ClientePago', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $form = $this->createDeleteForm($clientePago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($clientePago);
            $em->flush();
        }

        return $this->redirectToRoute('clientepago_index');
    }

    /**
     * Creates a form to delete a clientePago entity.
     *
     * @param ClientePago $clientePago The clientePago entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ClientePago $clientePago)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('clientepago_delete', array('id' => $clientePago->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
