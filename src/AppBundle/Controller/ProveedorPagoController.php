<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProveedorPago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Proveedorpago controller.
 *
 */
class ProveedorPagoController extends Controller
{
    /**
     * Lists all proveedorPago entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findAll();

        return $this->render('proveedorpago/index.html.twig', array(
            'proveedorPagos' => $proveedorPagos,
        ));
    }

    /**
     * Creates a new proveedorPago entity.
     *
     */
    public function newAction(Request $request)
    {
        $proveedorPago = new Proveedorpago();
        $form = $this->createForm('AppBundle\Form\ProveedorPagoType', $proveedorPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($proveedorPago);
            $em->flush();

            return $this->redirectToRoute('proveedorpago_show', array('id' => $proveedorPago->getId()));
        }

        return $this->render('proveedorpago/new.html.twig', array(
            'proveedorPago' => $proveedorPago,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proveedorPago entity.
     *
     */
    public function showAction(ProveedorPago $proveedorPago)
    {
        $deleteForm = $this->createDeleteForm($proveedorPago);

        return $this->render('proveedorpago/show.html.twig', array(
            'proveedorPago' => $proveedorPago,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proveedorPago entity.
     *
     */
    public function editAction(Request $request, ProveedorPago $proveedorPago)
    {
        $deleteForm = $this->createDeleteForm($proveedorPago);
        $editForm = $this->createForm('AppBundle\Form\ProveedorPagoType', $proveedorPago);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proveedorpago_edit', array('id' => $proveedorPago->getId()));
        }

        return $this->render('proveedorpago/edit.html.twig', array(
            'proveedorPago' => $proveedorPago,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proveedorPago entity.
     *
     */
    public function deleteAction(Request $request, ProveedorPago $proveedorPago)
    {
        $form = $this->createDeleteForm($proveedorPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proveedorPago);
            $em->flush();
        }

        return $this->redirectToRoute('proveedorpago_index');
    }

    /**
     * Creates a form to delete a proveedorPago entity.
     *
     * @param ProveedorPago $proveedorPago The proveedorPago entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ProveedorPago $proveedorPago)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedorpago_delete', array('id' => $proveedorPago->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
