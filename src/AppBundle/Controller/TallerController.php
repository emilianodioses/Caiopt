<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Taller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Taller controller.
 *
 */
class TallerController extends Controller
{
    /**
     * Lists all taller entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Taller')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('taller/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new taller entity.
     *
     */
    public function newAction(Request $request)
    {
        $taller = new Taller();
        $form = $this->createForm('AppBundle\Form\TallerType', $taller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $taller->setActivo(true);
            $taller->setCreatedBy($this->getUser());
            $taller->setCreatedAt(new \DateTime("now"));
            $taller->setUpdatedBy($this->getUser());
            $taller->setUpdatedAt(new \DateTime("now"));

            $em->persist($taller);
            $em->flush();

            return $this->redirectToRoute('taller_show', array('id' => $taller->getId()));
        }

        return $this->render('taller/new.html.twig', array(
            'taller' => $taller,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a taller entity.
     *
     */
    public function showAction(Taller $taller)
    {
        $deleteForm = $this->createDeleteForm($taller);

        return $this->render('taller/show.html.twig', array(
            'taller' => $taller,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing taller entity.
     *
     */
    public function editAction(Request $request, Taller $taller)
    {
        $deleteForm = $this->createDeleteForm($taller);
        $editForm = $this->createForm('AppBundle\Form\TallerType', $taller);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('taller_show', array('id' => $taller->getId()));
        }

        return $this->render('taller/edit.html.twig', array(
            'taller' => $taller,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a taller entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $taller = $em->getRepository('AppBundle:Taller')->find($id);

        if ($taller->getActivo() > 0)
            $taller->setActivo(0);
        else
            $taller->setActivo(1); 
        
        $taller->setUpdatedBy($this->getUser()); 
        $taller->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('taller_index');
    }

    /**
     * Creates a form to delete a taller entity.
     *
     * @param Taller $taller The taller entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Taller $taller)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('taller_delete', array('id' => $taller->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
