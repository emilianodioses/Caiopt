<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ReciboComprobante;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Recibocomprobante controller.
 *
 * @Route("recibocomprobante")
 */
class ReciboComprobanteController extends Controller
{
    /**
     * Lists all reciboComprobante entities.
     *
     * @Route("/", name="recibocomprobante_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ReciboComprobante', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findAll();

        return $this->render('recibocomprobante/index.html.twig', array(
            'reciboComprobantes' => $reciboComprobantes,
        ));
    }

    /**
     * Creates a new reciboComprobante entity.
     *
     * @Route("/new", name="recibocomprobante_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ReciboComprobante', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $reciboComprobante = new Recibocomprobante();
        $form = $this->createForm('AppBundle\Form\ReciboComprobanteType', $reciboComprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reciboComprobante);
            $em->flush();

            return $this->redirectToRoute('recibocomprobante_show', array('id' => $reciboComprobante->getId()));
        }

        return $this->render('recibocomprobante/new.html.twig', array(
            'reciboComprobante' => $reciboComprobante,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a reciboComprobante entity.
     *
     * @Route("/{id}", name="recibocomprobante_show")
     * @Method("GET")
     */
    public function showAction(ReciboComprobante $reciboComprobante)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ReciboComprobante', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($reciboComprobante);

        return $this->render('recibocomprobante/show.html.twig', array(
            'reciboComprobante' => $reciboComprobante,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing reciboComprobante entity.
     *
     * @Route("/{id}/edit", name="recibocomprobante_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, ReciboComprobante $reciboComprobante)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ReciboComprobante', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($reciboComprobante);
        $editForm = $this->createForm('AppBundle\Form\ReciboComprobanteType', $reciboComprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recibocomprobante_edit', array('id' => $reciboComprobante->getId()));
        }

        return $this->render('recibocomprobante/edit.html.twig', array(
            'reciboComprobante' => $reciboComprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a reciboComprobante entity.
     *
     * @Route("/{id}", name="recibocomprobante_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, ReciboComprobante $reciboComprobante)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ReciboComprobante', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $form = $this->createDeleteForm($reciboComprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reciboComprobante);
            $em->flush();
        }

        return $this->redirectToRoute('recibocomprobante_index');
    }

    /**
     * Creates a form to delete a reciboComprobante entity.
     *
     * @param ReciboComprobante $reciboComprobante The reciboComprobante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ReciboComprobante $reciboComprobante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recibocomprobante_delete', array('id' => $reciboComprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
