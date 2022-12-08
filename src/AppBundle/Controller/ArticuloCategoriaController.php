<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use AppBundle\Entity\ArticuloCategoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * ArticuloCategoria controller.
 *
 */
class ArticuloCategoriaController extends Controller
{
    /**
     * Lists all articulomarca entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloCategoria', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ArticuloCategoria')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            15
        );

        return $this->render('articulocategoria/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }
    /**
     * Finds and displays a articuloMarca entity.
     *
     */
    public function showAction(ArticuloCategoria $articulocategoria, Request $request)
    {
        return $this->render('articulocategoria/show.html.twig', array(
            'articulocategoria' => $articulocategoria,
        ));
    }

    /**
     * Creates a new articuloMarca entity.
     *
     */
    public function newAction(Request $request)
    {
        $articulocategoria = new ArticuloCategoria();
        $form = $this->createForm('AppBundle\Form\ArticuloCategoriaType', $articulocategoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $categoria_existente = $em->getRepository('AppBundle:ArticuloCategoria')->findBy(Array('descripcion' => $articulocategoria->getDescripcion()));

            if (count($categoria_existente) > 0)
            {
                if ($categoria_existente[0]->getActivo())
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe una categoria con la descripcion ingresada.');
                else
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe una categoria con la descripcion ingresada con estado inactivo.');

                return $this->render('articulocategoria/new.html.twig', array(
                    'articulocategoria' => $articulocategoria,
                    'form' => $form->createView(),
                ));
            }

            $articulocategoria->setActivo(true);
            $articulocategoria->setCreatedBy($this->getUser());
            $articulocategoria->setCreatedAt(new \DateTime("now"));
            $articulocategoria->setUpdatedBy($this->getUser());
            $articulocategoria->setUpdatedAt(new \DateTime("now"));

            $em->persist($articulocategoria);
            $em->flush();

            return $this->redirectToRoute('articulocategoria_show', array('id' => $articulocategoria->getId()));
        }

        return $this->render('articulocategoria/new.html.twig', array(
            'articulocategoria' => $articulocategoria,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, ArticuloCategoria $articulocategoria)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloCategoria', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $editForm = $this->createForm('AppBundle\Form\ArticuloCategoriaType', $articulocategoria);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $articulocategoria_existente = $em->getRepository('AppBundle:ArticuloCategoria')->findBy(Array('id' => $articulocategoria->getid(), 'activo' => 1));

            if(count($articulocategoria_existente) == 1 && $articulocategoria_existente[0]->getId() != $articulocategoria->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe una categoria con el nombre ingresado.');
                return $this->render('articulocategoria/edit.html.twig', array(
                    'articulocategoria' => $articulocategoria,
                    'edit_form' => $editForm->createView(),
                ));
            }

            $articulocategoria->setUpdatedBy($this->getUser());
            $articulocategoria->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('articulocategoria_index');
        }

        return $this->render('articulocategoria/edit.html.twig', array(
            'articulocategoria' => $articulocategoria,
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

        if (!$secure->isAuthorized('ArticuloCategoria', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $articulocategoria = $em->getRepository('AppBundle:ArticuloCategoria')->find($id);
        if ($articulocategoria->getActivo() > 0)
            $articulocategoria->setActivo(0);
        else
            $articulocategoria->setActivo(1);

        $articulocategoria->setUpdatedBy($this->getUser());
        $articulocategoria->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('articulocategoria_index');
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