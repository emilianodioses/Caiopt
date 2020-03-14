<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ObraSocialPlan;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Obrasocialplan controller.
 *
 */
class ObraSocialPlanController extends Controller
{
    /**
     * Lists all obraSocial entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ObraSocialPlan')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('obrasocialplan/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new obraSocialPlan entity.
     *
     */
    public function newAction(Request $request)
    {
        $obrasocialplan = new ObraSocialPlan();
        $form = $this->createForm('AppBundle\Form\ObraSocialPlanType', $obrasocialplan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $obrasocialplan->setActivo(true);
            $obrasocialplan->setCreatedBy($this->getUser());
            $obrasocialplan->setCreatedAt(new \DateTime("now"));
            $obrasocialplan->setUpdatedBy($this->getUser());
            $obrasocialplan->setUpdatedAt(new \DateTime("now"));
            
            $em->persist($obrasocialplan);
            $em->flush();

            return $this->redirectToRoute('obrasocialplan_show', array('id' => $obrasocialplan->getId()));
        }

        return $this->render('obrasocialplan/new.html.twig', array(
            'obrasocialplan' => $obrasocialplan,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a obraSocialPlan entity.
     *
     */
    public function showAction(ObraSocialPlan $obrasocialplan)
    {
        $deleteForm = $this->createDeleteForm($obrasocialplan);

        return $this->render('obrasocialplan/show.html.twig', array(
            'obrasocialplan' => $obrasocialplan,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing obraSocialPlan entity.
     *
     */
    public function editAction(Request $request, ObraSocialPlan $obrasocialplan)
    {
        $deleteForm = $this->createDeleteForm($obrasocialplan);
        $editForm = $this->createForm('AppBundle\Form\ObraSocialPlanType', $obrasocialplan);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $obrasocialplan->setUpdatedBy($this->getUser());
            $obrasocialplan->setUpdatedAt(new \DateTime("now"));
            
            $em->flush();

            return $this->redirectToRoute('obrasocialplan_show', array('id' => $obrasocialplan->getId()));
        }

        return $this->render('obrasocialplan/edit.html.twig', array(
            'obrasocialplan' => $obrasocialplan,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a obraSocialPlan entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $obrasocialplan = $em->getRepository('AppBundle:ObraSocialPlan')->find($id);

        if ($obrasocialplan->getActivo() > 0)
            $obrasocialplan->setActivo(0);
        else
            $obrasocialplan->setActivo(1); 
        
        $obrasocialplan->setUpdatedBy($this->getUser()); 
        $obrasocialplan->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('obrasocialplan_index');
    }

    /**
     * Creates a form to delete a obraSocialPlan entity.
     *
     * @param ObraSocialPlan $obraSocialPlan The obraSocialPlan entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ObraSocialPlan $obraSocialPlan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('obrasocialplan_delete', array('id' => $obraSocialPlan->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
