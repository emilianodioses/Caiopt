<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ObraSocial;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Obrasocial controller.
 *
 */
class ObraSocialController extends Controller
{
    /**
     * Lists all obraSocial entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ObraSocial')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('obrasocial/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new obraSocial entity.
     *
     */
    public function newAction(Request $request)
    {
        $obrasocial = new Obrasocial();
        $form = $this->createForm('AppBundle\Form\ObraSocialType', $obrasocial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $obrasocial->setActivo(true);
            $obrasocial->setCreatedBy($this->getUser());
            $obrasocial->setCreatedAt(new \DateTime("now"));
            $obrasocial->setUpdatedBy($this->getUser());
            $obrasocial->setUpdatedAt(new \DateTime("now"));
            
            $em->persist($obrasocial);
            $em->flush();

            return $this->redirectToRoute('obrasocial_show', array('id' => $obrasocial->getId()));
        }

        return $this->render('obrasocial/new.html.twig', array(
            'obrasocial' => $obrasocial,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a obraSocial entity.
     *
     */
    public function showAction(ObraSocial $obrasocial)
    {
        $deleteForm = $this->createDeleteForm($obrasocial);

        return $this->render('obrasocial/show.html.twig', array(
            'obrasocial' => $obrasocial,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing obraSocial entity.
     *
     */
    public function editAction(Request $request, ObraSocial $obrasocial)
    {
        $deleteForm = $this->createDeleteForm($obrasocial);
        $editForm = $this->createForm('AppBundle\Form\ObraSocialType', $obrasocial);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('obrasocial_show', array('id' => $obrasocial->getId()));
        }

        return $this->render('obrasocial/edit.html.twig', array(
            'obrasocial' => $obrasocial,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a obraSocial entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $obrasocial = $em->getRepository('AppBundle:ObraSocial')->find($id);

        if ($obrasocial->getActivo() > 0)
            $obrasocial->setActivo(0);
        else
            $obrasocial->setActivo(1); 
        
        $obrasocial->setUpdatedBy($this->getUser()); 
        $obrasocial->setUpdatedAt(new \DateTime("now")); 

        $em->flush($obrasocial);

        return $this->redirectToRoute('obrasocial_index');
    }

    /**
     * Creates a form to delete a obraSocial entity.
     *
     * @param ObraSocial $obraSocial The obraSocial entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ObraSocial $obrasocial)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('obrasocial_delete', array('id' => $obrasocial->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
