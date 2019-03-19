<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ordentrabajodetalle controller.
 *
 */
class OrdenTrabajoDetalleController extends Controller
{
    /**
     * Lists all ordenTrabajoDetalle entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ordenTrabajoDetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findAll();

        return $this->render('ordentrabajodetalle/index.html.twig', array(
            'ordenTrabajoDetalles' => $ordenTrabajoDetalles,
        ));
    }

    /**
     * Creates a new ordenTrabajoDetalle entity.
     *
     */
    public function newAction(Request $request)
    {
        $ordenTrabajoDetalle = new Ordentrabajodetalle();
        $form = $this->createForm('AppBundle\Form\OrdenTrabajoDetalleType', $ordenTrabajoDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ordenTrabajoDetalle);
            $em->flush();

            return $this->redirectToRoute('ordentrabajodetalle_show', array('id' => $ordenTrabajoDetalle->getId()));
        }

        return $this->render('ordentrabajodetalle/new.html.twig', array(
            'ordenTrabajoDetalle' => $ordenTrabajoDetalle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ordenTrabajoDetalle entity.
     *
     */
    public function showAction(OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        $deleteForm = $this->createDeleteForm($ordenTrabajoDetalle);

        return $this->render('ordentrabajodetalle/show.html.twig', array(
            'ordenTrabajoDetalle' => $ordenTrabajoDetalle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenTrabajoDetalle entity.
     *
     */
    public function editAction(Request $request, OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        $deleteForm = $this->createDeleteForm($ordenTrabajoDetalle);
        $editForm = $this->createForm('AppBundle\Form\OrdenTrabajoDetalleType', $ordenTrabajoDetalle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ordentrabajodetalle_edit', array('id' => $ordenTrabajoDetalle->getId()));
        }

        return $this->render('ordentrabajodetalle/edit.html.twig', array(
            'ordenTrabajoDetalle' => $ordenTrabajoDetalle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ordenTrabajoDetalle entity.
     *
     */
    public function deleteAction(Request $request, OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        $form = $this->createDeleteForm($ordenTrabajoDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ordenTrabajoDetalle);
            $em->flush();
        }

        return $this->redirectToRoute('ordentrabajodetalle_index');
    }

    /**
     * Creates a form to delete a ordenTrabajoDetalle entity.
     *
     * @param OrdenTrabajoDetalle $ordenTrabajoDetalle The ordenTrabajoDetalle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenTrabajoDetalle $ordenTrabajoDetalle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordentrabajodetalle_delete', array('id' => $ordenTrabajoDetalle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
