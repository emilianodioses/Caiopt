<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recibo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Recibo controller.
 *
 */
class ReciboController extends Controller
{
    /**
     * Lists all recibo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $recibos = $em->getRepository('AppBundle:Recibo')->findAll();

        return $this->render('recibo/index.html.twig', array(
            'recibos' => $recibos,
        ));
    }

    /**
     * Creates a new recibo entity.
     *
     */
    public function newAction(Request $request)
    {
        $recibo = new Recibo();
        $form = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recibo);
            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }

        return $this->render('recibo/new.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a recibo entity.
     *
     */
    public function showAction(Recibo $recibo)
    {
        $deleteForm = $this->createDeleteForm($recibo);

        return $this->render('recibo/show.html.twig', array(
            'recibo' => $recibo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing recibo entity.
     *
     */
    public function editAction(Request $request, Recibo $recibo)
    {
        $deleteForm = $this->createDeleteForm($recibo);
        $editForm = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recibo_edit', array('id' => $recibo->getId()));
        }

        return $this->render('recibo/edit.html.twig', array(
            'recibo' => $recibo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a recibo entity.
     *
     */
    public function deleteAction(Request $request, Recibo $recibo)
    {
        $form = $this->createDeleteForm($recibo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recibo);
            $em->flush();
        }

        return $this->redirectToRoute('recibo_index');
    }

    /**
     * Creates a form to delete a recibo entity.
     *
     * @param Recibo $recibo The recibo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recibo $recibo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recibo_delete', array('id' => $recibo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
