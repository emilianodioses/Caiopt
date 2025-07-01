<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PresupuestoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * PresupuestoDetalle controller.
 *
 */
class PresupuestoDetalleController extends Controller
{
    /**
     * Lists all presupuestoDetalle entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
//        if (!$secure->isAuthorized('OrdenTrabajoDetalle', 'Index', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $em = $this->getDoctrine()->getManager();

        $presupuestoDetalles = $em->getRepository('AppBundle:PresupuestoDetalle')->findAll();

        return $this->render('presupuestoDetalle/index.html.twig', array(
            'presupuestoDetalles' => $presupuestoDetalles,
        ));
    }

    /**
     * Creates a new presupuestoDetalle entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
//        if (!$secure->isAuthorized('OrdenTrabajoDetalle', 'New', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $presupuestoDetalle = new PresupuestoDetalle();
        $form = $this->createForm('AppBundle\Form\PresupuestoDetalleType', $presupuestoDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($presupuestoDetalle);
            $em->flush();

            return $this->redirectToRoute('presupuestodetalle_show', array('id' => $presupuestoDetalle->getId()));
        }

        return $this->render('presupuestodetalle/new.html.twig', array(
            'presupuestoDetalle' => $presupuestoDetalle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a presupuestodetalle entity.
     *
     */
    public function showAction(PresupuestoDetalle $presupuestoDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

//        if (!$secure->isAuthorized('PresupuestoDetalle', 'Show', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $deleteForm = $this->createDeleteForm($presupuestoDetalle);

        return $this->render('presupuestodetalle/show.html.twig', array(
            'presupuestoDetalle' => $presupuestoDetalle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing presupuestodetalle entity.
     *
     */
    public function editAction(Request $request, PresupuestoDetalle $presupuestoDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

//        if (!$secure->isAuthorized('OrdenTrabajoDetalle', 'Edit', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $deleteForm = $this->createDeleteForm($presupuestoDetalle);
        $editForm = $this->createForm('AppBundle\Form\PresupuestoDetalleType', $presupuestoDetalle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('presupuestodetalle_edit', array('id' => $presupuestoDetalle->getId()));
        }

        return $this->render('presupuestodetalle/edit.html.twig', array(
            'presupuestoDetalle' => $presupuestoDetalle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a presupuestodetalle entity.
     *
     */
    public function deleteAction(Request $request, PresupuestoDetalle $presupuestoDetalle)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

//        if (!$secure->isAuthorized('OrdenTrabajoDetalle', 'Delete', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $form = $this->createDeleteForm($presupuestoDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($presupuestoDetalle);
            $em->flush();
        }

        return $this->redirectToRoute('presupuestodetalle_index');
    }

    /**
     * Creates a form to delete a presupuestoDetalle entity.
     *
     * @param PresupuestoDetalle $presupuestoDetalle The presupuestoDetalle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PresupuestoDetalle $presupuestoDetalle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('presupuestodetalle_delete', array('id' => $presupuestoDetalle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
