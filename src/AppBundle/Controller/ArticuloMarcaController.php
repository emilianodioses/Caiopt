<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use AppBundle\Entity\ArticuloMarca;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * ArticuloMarca controller.
 *
 */
class ArticuloMarcaController extends Controller
{
    /**
     * Lists all articulomarca entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloMarca', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ArticuloMarca')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            15
        );

        return $this->render('articulomarca/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }
    /**
     * Finds and displays a articuloMarca entity.
     *
     */
    public function showAction(ArticuloMarca $articulomarca, Request $request)
    {
        return $this->render('articulomarca/show.html.twig', array(
            'articulomarca' => $articulomarca,
        ));
    }

    /**
     * Creates a new articuloMarca entity.
     *
     */
    public function newAction(Request $request)
    {
        $articulomarca = new ArticuloMarca();
        $form = $this->createForm('AppBundle\Form\ArticuloMarcaType', $articulomarca);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $marca_existente = $em->getRepository('AppBundle:ArticuloMarca')->findBy(Array('descripcion' => $articulomarca->getDescripcion()));

            if (count($marca_existente) > 0)
            {
                if ($marca_existente[0]->getActivo())
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe una marca con la descripcion ingresada.');
                else
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe una marca con la descripcion ingresada con estado inactivo.');

                return $this->render('articulomarca/new.html.twig', array(
                    'articulomarca' => $articulomarca,
                    'form' => $form->createView(),
                ));
            }

            $articulomarca->setActivo(true);
            $articulomarca->setCreatedBy($this->getUser());
            $articulomarca->setCreatedAt(new \DateTime("now"));
            $articulomarca->setUpdatedBy($this->getUser());
            $articulomarca->setUpdatedAt(new \DateTime("now"));

            $em->persist($articulomarca);
            $em->flush();

            return $this->redirectToRoute('articulomarca_show', array('id' => $articulomarca->getId()));
        }

        return $this->render('articulomarca/new.html.twig', array(
            'articulomarca' => $articulomarca,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, ArticuloMarca $articulomarca)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloMarca', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $editForm = $this->createForm('AppBundle\Form\ArticuloMarcaType', $articulomarca);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $articulomarca_existente = $em->getRepository('AppBundle:ArticuloMarca')->findBy(Array('id' => $articulomarca->getid(), 'activo' => 1));

            if(count($articulomarca_existente) == 1 && $articulomarca_existente[0]->getId() != $articulomarca->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un articulomarca con el nombre ingresado.');
                return $this->render('articulomarca/edit.html.twig', array(
                    'articulomarca' => $articulomarca,
                    'edit_form' => $editForm->createView(),
                ));
            }

            $articulomarca->setUpdatedBy($this->getUser());
            $articulomarca->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('articulomarca_index');
        }

        return $this->render('articulomarca/edit.html.twig', array(
            'articulomarca' => $articulomarca,
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

        if (!$secure->isAuthorized('ArticuloMarca', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $articulomarca = $em->getRepository('AppBundle:ArticuloMarca')->find($id);
        if ($articulomarca->getActivo() > 0)
            $articulomarca->setActivo(0);
        else
            $articulomarca->setActivo(1);

        $articulomarca->setUpdatedBy($this->getUser());
        $articulomarca->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('articulomarca_index');
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
