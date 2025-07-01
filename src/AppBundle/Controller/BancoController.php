<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Banco;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Banco controller.
 *
 */
class BancoController extends Controller
{
    /**
     * Lists all banco entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Banco')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('banco/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new banco entity.
     *
     */
    public function newAction(Request $request)
    {
        $banco = new Banco();
        $form = $this->createForm('AppBundle\Form\BancoType', $banco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $banco->setActivo(true);
            $banco->setCreatedBy($this->getUser());
            $banco->setCreatedAt(new \DateTime("now"));
            $banco->setUpdatedBy($this->getUser());
            $banco->setUpdatedAt(new \DateTime("now"));

            $em->persist($banco);
            $em->flush();

            return $this->redirectToRoute('banco_show', array('id' => $banco->getId()));
        }

        return $this->render('banco/new.html.twig', array(
            'banco' => $banco,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a banco entity.
     *
     */
    public function showAction(Banco $banco)
    {
        $deleteForm = $this->createDeleteForm($banco);

        return $this->render('banco/show.html.twig', array(
            'banco' => $banco,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing banco entity.
     *
     */
    public function editAction(Request $request, Banco $banco)
    {
        $deleteForm = $this->createDeleteForm($banco);
        $editForm = $this->createForm('AppBundle\Form\BancoType', $banco);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $banco->setUpdatedBy($this->getUser());
            $banco->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('banco_show', array('id' => $banco->getId()));
        }

        return $this->render('banco/edit.html.twig', array(
            'banco' => $banco,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a banco entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $banco = $em->getRepository('AppBundle:Banco')->find($id);

        if ($banco->getActivo() > 0)
            $banco->setActivo(0);
        else
            $banco->setActivo(1); 
        
        $banco->setUpdatedBy($this->getUser()); 
        $banco->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('banco_index');
    }

    /**
     * Creates a form to delete a banco entity.
     *
     * @param Banco $banco The banco entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Banco $banco)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('banco_delete', array('id' => $banco->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
