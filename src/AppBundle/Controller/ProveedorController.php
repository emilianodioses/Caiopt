<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proveedor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Proveedor controller.
 *
 */
class ProveedorController extends Controller
{
    /**
     * Lists all proveedor entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Proveedor', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Proveedor')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
        return $this->render('proveedor/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new proveedor entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Proveedor', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $proveedor = new Proveedor();
        $form = $this->createForm('AppBundle\Form\ProveedorType', $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $proveedor->setActivo(true);
            $proveedor->setCreatedBy($this->getUser()->getId());
            $proveedor->setCreatedAt(new \DateTime("now"));
            $proveedor->setUpdatedBy($this->getUser()->getId());
            $proveedor->setUpdatedAt(new \DateTime("now"));

            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor_index');
        }

        return $this->render('proveedor/new.html.twig', array(
            'proveedor' => $proveedor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proveedor entity.
     *
     */
    public function showAction(Proveedor $proveedor)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Proveedor', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('proveedor'=>$proveedor, 'movimiento'=>'Compra', 'activo' => 1));

        $ordenPagos = $em->getRepository('AppBundle:OrdenPago')->findBy(Array('proveedor'=>$proveedor, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($proveedor);

        return $this->render('proveedor/show.html.twig', array(
            'proveedor' => $proveedor,
            'comprobantes' => $comprobantes,
            'ordenPagos' => $ordenPagos,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proveedor entity.
     *
     */
    public function editAction(Request $request, Proveedor $proveedor)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Proveedor', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($proveedor);
        $editForm = $this->createForm('AppBundle\Form\ProveedorType', $proveedor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $proveedor->setUpdatedBy($this->getUser()->getId());
            $proveedor->setUpdatedAt(new \DateTime("now"));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proveedor_index');
        }

        return $this->render('proveedor/edit.html.twig', array(
            'proveedor' => $proveedor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proveedor entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Proveedor', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $proveedor = $em->getRepository('AppBundle:Proveedor')->find($id);
        if ($proveedor->getActivo() > 0)
            $proveedor->setActivo(0);
        else
            $proveedor->setActivo(1);  
        
        $proveedor->setUpdatedBy($this->getUser()->getId());
        $proveedor->setUpdatedAt(new \DateTime("now"));

        $em->flush($proveedor);

        return $this->redirectToRoute('proveedor_index');
    }

    /**
     * Creates a form to delete a proveedor entity.
     *
     * @param Proveedor $proveedor The proveedor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proveedor $proveedor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedor_delete', array('id' => $proveedor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function findSelect2Action(Request $request) {
        $em = $em = $this->getDoctrine()->getManager('default');
        
        $text_search = $request->get('q');
        $pageLimit = $request->get('page_limit');

        if (!is_numeric($pageLimit) || $pageLimit > 10) {
            $pageLimit = 10;
        }

        $result = $em->createQuery('
                        SELECT r.id as id, CONCAT(r.nombre, \' (\', r.documentoNumero, \')\')  as text
                        FROM AppBundle:Proveedor r
                        WHERE (lower(r.nombre) LIKE :text_search OR r.documentoNumero LIKE :text_search)
                        AND r.activo = 1
                        ORDER BY r.nombre ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }
}
