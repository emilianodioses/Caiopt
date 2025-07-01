<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use AppBundle\Entity\ArticuloMarco;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * ArticuloMarco controller.
 *
 */
class ArticuloMarcoController extends Controller
{
    /**
     * Lists all articulomarco entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloMarco', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:ArticuloMarco')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            15
        );

        return $this->render('articulomarco/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }
    /**
     * Finds and displays a articulomarco entity.
     *
     */
    public function showAction(ArticuloMarco $articulomarco, Request $request)
    {
        return $this->render('articulomarco/show.html.twig', array(
            'articulomarco' => $articulomarco,
        ));
    }

    /**
     * Creates a new articuloMarco entity.
     *
     */
    public function newAction(Request $request)
    {
        $articulomarco = new ArticuloMarco();
        $form = $this->createForm('AppBundle\Form\ArticuloMarcoType', $articulomarco);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $marco_existente = $em->getRepository('AppBundle:ArticuloMarco')->findBy(Array('descripcion' => $articulomarco->getDescripcion()));

            if (count($marco_existente) > 0)
            {
                if ($marco_existente[0]->getActivo())
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe un marco con la descripcion ingresada.');
                else
                    $this->get('session')->getFlashbag()->add('warning', 'Ya existe un marco con la descripcion ingresada con estado inactivo.');

                return $this->render('articulomarco/new.html.twig', array(
                    'articulomarco' => $articulomarco,
                    'form' => $form->createView(),
                ));
            }

            $articulomarco->setActivo(true);
            $articulomarco->setCreatedBy($this->getUser());
            $articulomarco->setCreatedAt(new \DateTime("now"));
            $articulomarco->setUpdatedBy($this->getUser());
            $articulomarco->setUpdatedAt(new \DateTime("now"));

            $em->persist($articulomarco);
            $em->flush();

            return $this->redirectToRoute('articulomarco_show', array('id' => $articulomarco->getId()));
        }

        return $this->render('articulomarco/new.html.twig', array(
            'articulomarco' => $articulomarco,
            'form' => $form->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, ArticuloMarco $articulomarco)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('ArticuloMarco', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $editForm = $this->createForm('AppBundle\Form\ArticuloMarcoType', $articulomarco);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $articulomarco_existente = $em->getRepository('AppBundle:ArticuloMarco')->findBy(Array('id' => $articulomarco->getid(), 'activo' => 1));

            if(count($articulomarco_existente) == 1 && $articulomarco_existente[0]->getId() != $articulomarco->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un articulomarco con el nombre ingresado.');
                return $this->render('articulomarco/edit.html.twig', array(
                    'articulomarco' => $articulomarco,
                    'edit_form' => $editForm->createView(),
                ));
            }

            $articulomarco->setUpdatedBy($this->getUser());
            $articulomarco->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('articulomarco_index');
        }

        return $this->render('articulomarco/edit.html.twig', array(
            'articulomarco' => $articulomarco,
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

        if (!$secure->isAuthorized('ArticuloMarco', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $articulomarco = $em->getRepository('AppBundle:ArticuloMarco')->find($id);
        if ($articulomarco->getActivo() > 0)
            $articulomarco->setActivo(0);
        else
            $articulomarco->setActivo(1);

        $articulomarco->setUpdatedBy($this->getUser());
        $articulomarco->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('articulomarco_index');
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
