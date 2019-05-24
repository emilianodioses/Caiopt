<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rol;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Rol controller.
 *
 */
class RolController extends Controller
{
    /**
     * Lists all rol entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //$texto = $request->get('texto','');

        //$query = $em->getRepository('AppBundle:Rol')->findByTexto($texto);

        $query = $em->getRepository('AppBundle:Rol')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('rol/index.html.twig', array(
            'pagination' => $pagination,
            //'texto' => $texto
        ));
    }

    /**
     * Finds and displays a rol entity.
     *
     */
    public function showAction(Rol $rol)
    {
        $em = $this->getDoctrine()->getManager();

        $rolFuncion = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));

        return $this->render('rol/show.html.twig', array(
            'rol' => $rol,
            'rolFuncion' => $rolFuncion,
        ));
    }

    /**
     * Creates a new rol entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol = new Rol();
        $form = $this->createForm('AppBundle\Form\RolType', $rol);
        $form->handleRequest($request);

        $rolFuncion = $em->getRepository('AppBundle:RolFuncion')->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rol->setActivo(true);
            $rol->setCreatedBy($this->getUser()->getId());
            $rol->setCreatedAt(new \DateTime("now"));
            $rol->setUpdatedBy($this->getUser()->getId());
            $rol->setUpdatedAt(new \DateTime("now"));

            $em->persist($rol);
            $em->flush();

            return $this->redirectToRoute('rol_show', array('id' => $rol->getId()));
        }

        return $this->render('rol/new.html.twig', array(
            'rol' => $rol,
            'rolFuncion' => $rolFuncion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, Rol $rol)
    {
        $deleteForm = $this->createDeleteForm($rol);
        $editForm = $this->createForm('AppBundle\Form\RolType', $rol);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $rolFuncion = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));

        if ($editForm->isSubmitted() && $editForm->isValid()) {             
            $rol->setUpdatedBy($this->getUser()->getId());
            $rol->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('rol_index');
        }

        return $this->render('rol/edit.html.twig', array(
            'rol' => $rol,
            'rolFuncion' => $rolFuncion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a rol entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $rol = $em->getRepository('AppBundle:Rol')->find($id);
        if ($rol->getActivo() > 0)
            $rol->setActivo(0);
        else
            $rol->setActivo(1);  

        $rol->setUpdatedBy($this->getUser()->getId()); 
        $rol->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($rol);
        
        return $this->redirectToRoute('rol_index');
    }

    /**
     * Creates a form to delete a rol entity.
     *
     * @param Usuario $rol The usuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Rol $rol)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('rol_delete', array('id' => $rol->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
