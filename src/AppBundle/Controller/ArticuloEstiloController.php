<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use AppBundle\Entity\ArticuloEstilo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * ArticuloEstilo controller.
 *
 */
class ArticuloEstiloController extends Controller
{
    /**
     * Lists all articuloestilo entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloEstilo', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ArticuloEstilo')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            15
        );

        return $this->render('articuloestilo/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }
    /**
     * Finds and displays a articuloestilo entity.
     *
     */
    public function showAction(ArticuloEstilo $articuloestilo, Request $request)
    {
        return $this->render('articuloestilo/show.html.twig', array(
            'articuloestilo' => $articuloestilo,
        ));
    }

    /**
     * Creates a new articuloEstilo entity.
     *
     */
    public function newAction(Request $request)
    {
        $articuloestilo = new ArticuloEstilo();
        $form = $this->createForm('AppBundle\Form\ArticuloEstiloType', $articuloestilo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $estilo_existente = $em->getRepository('AppBundle:ArticuloEstilo')->findBy(Array('descripcion' => $articuloestilo->getDescripcion()));

            if (count($estilo_existente) > 0)
            {
                if ($estilo_existente[0]->getActivo())
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe un estilo con la descripcion ingresada.');
                else
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe un estilo con la descripcion ingresada con estado inactivo.');

                return $this->render('articuloestilo/new.html.twig', array(
                    'articuloestilo' => $articuloestilo,
                    'form' => $form->createView(),
                ));
            }

            $articuloestilo->setActivo(true);
            $articuloestilo->setCreatedBy($this->getUser());
            $articuloestilo->setCreatedAt(new \DateTime("now"));
            $articuloestilo->setUpdatedBy($this->getUser());
            $articuloestilo->setUpdatedAt(new \DateTime("now"));

            $em->persist($articuloestilo);
            $em->flush();

            return $this->redirectToRoute('articuloestilo_show', array('id' => $articuloestilo->getId()));
        }

        return $this->render('articuloestilo/new.html.twig', array(
            'articuloestilo' => $articuloestilo,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, ArticuloEstilo $articuloestilo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloEstilo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $editForm = $this->createForm('AppBundle\Form\ArticuloEstiloType', $articuloestilo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $articuloestilo_existente = $em->getRepository('AppBundle:ArticuloEstilo')->findBy(Array('id' => $articuloestilo->getid(), 'activo' => 1));

            if(count($articuloestilo_existente) == 1 && $articuloestilo_existente[0]->getId() != $articuloestilo->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un articuloestilo con el nombre ingresado.');
                return $this->render('articuloestilo/edit.html.twig', array(
                    'articuloestilo' => $articuloestilo,
                    'edit_form' => $editForm->createView(),
                ));
            }

            $articuloestilo->setUpdatedBy($this->getUser());
            $articuloestilo->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('articuloestilo_index');
        }

        return $this->render('articuloestilo/edit.html.twig', array(
            'articuloestilo' => $articuloestilo,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a rol entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloEstilo', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $articuloestilo = $em->getRepository('AppBundle:ArticuloEstilo')->find($id);
        if ($articuloestilo->getActivo() > 0)
            $articuloestilo->setActivo(0);
        else
            $articuloestilo->setActivo(1);

        $articuloestilo->setUpdatedBy($this->getUser());
        $articuloestilo->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('articuloestilo_index');
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
