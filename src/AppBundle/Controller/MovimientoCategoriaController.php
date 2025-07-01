<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MovimientoCategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * MovimientoCategoria controller.
 *
 */
class MovimientoCategoriaController extends Controller
{
    /**
     * Lists all movimientoCategoria entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:MovimientoCategoria')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('movimientocategoria/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new movimientoCategoria entity.
     *
     */
    public function newAction(Request $request)
    {
        $movimientoCategoria = new MovimientoCategoria();
        $form = $this->createForm('AppBundle\Form\MovimientoCategoriaType', $movimientoCategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $movimientoCategoria->setActivo(true);
            $movimientoCategoria->setCreatedBy($this->getUser());
            $movimientoCategoria->setCreatedAt(new \DateTime("now"));
            $movimientoCategoria->setUpdatedBy($this->getUser());
            $movimientoCategoria->setUpdatedAt(new \DateTime("now"));

            $em->persist($movimientoCategoria);
            $em->flush();

            return $this->redirectToRoute('movimientocategoria_show', array('id' => $movimientoCategoria->getId()));
        }

        return $this->render('movimientocategoria/new.html.twig', array(
            'movimientoCategoria' => $movimientoCategoria,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a movimientoCategoria entity.
     *
     */
    public function showAction(MovimientoCategoria $movimientoCategoria)
    {
        $deleteForm = $this->createDeleteForm($movimientoCategoria);

        return $this->render('movimientocategoria/show.html.twig', array(
            'movimientoCategoria' => $movimientoCategoria,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing movimientoCategoria entity.
     *
     */
    public function editAction(Request $request, MovimientoCategoria $movimientoCategoria)
    {
        $deleteForm = $this->createDeleteForm($movimientoCategoria);
        $editForm = $this->createForm('AppBundle\Form\MovimientoCategoriaType', $movimientoCategoria);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $movimientoCategoria->setUpdatedBy($this->getUser());
            $movimientoCategoria->setUpdatedAt(new \DateTime("now"));
            
            $em->flush();

            return $this->redirectToRoute('movimientocategoria_show', array('id' => $movimientoCategoria->getId()));
        }

        return $this->render('movimientocategoria/edit.html.twig', array(
            'movimientoCategoria' => $movimientoCategoria,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a movimientoCategoria entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $movimientoCategoria = $em->getRepository('AppBundle:MovimientoCategoria')->find($id);

        if ($movimientoCategoria->getActivo() > 0)
            $movimientoCategoria->setActivo(0);
        else
            $movimientoCategoria->setActivo(1); 
        
        $movimientoCategoria->setUpdatedBy($this->getUser()); 
        $movimientoCategoria->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('movimientocategoria_index');
    }

    /**
     * Creates a form to delete a movimientoCategoria entity.
     *
     * @param MovimientoCategoria $movimientoCategoria The movimientoCategoria entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(MovimientoCategoria $movimientoCategoria)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movimientocategoria_delete', array('id' => $movimientoCategoria->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
