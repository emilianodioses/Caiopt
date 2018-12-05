<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comprobante;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ComprobanteType;

/**
 * Comprobante controller.
 *
 */
class ComprobanteVentaController extends Controller
{
    /**
     * Lists all comprobante entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('tipo' => 'venta'));

        return $this->render('comprobanteventa/index.html.twig', array(
            'comprobantes' => $comprobantes,
        ));
    }

    /**
     * Creates a new comprobante entity.
     *
     */
    public function newAction(Request $request)
    {
        $comprobante = new Comprobante();
        $form = $this->createForm(ComprobanteType::Class, $comprobante);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            echo '<pre>';
            var_export($form->getData());
            die;
            $em = $this->getDoctrine()->getManager();
            $comprobante->setMovimiento('Venta');
            $em->persist($comprobante);
            $em->flush();

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/new.html.twig', array(
            'comprobante' => $comprobante,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comprobante entity.
     *
     */
    public function showAction(Comprobante $comprobante)
    {
        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobanteventa/show.html.twig', array(
            'comprobante' => $comprobante,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comprobante entity.
     *
     */
    public function editAction(Request $request, Comprobante $comprobante)
    {
        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comprobanteventa_edit', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comprobante entity.
     *
     */
    public function deleteAction(Request $request, Comprobante $comprobante)
    {
        $form = $this->createDeleteForm($comprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comprobante);
            $em->flush();
        }

        return $this->redirectToRoute('comprobanteventa_index');
    }

    /**
     * Creates a form to delete a comprobante entity.
     *
     * @param Comprobante $comprobante The comprobante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comprobante $comprobante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comprobanteventa_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
