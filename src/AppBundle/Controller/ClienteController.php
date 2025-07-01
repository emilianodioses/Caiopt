<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cliente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Presupuesto;

/**
 * Cliente controller.
 *
 */
class ClienteController extends Controller
{
    /**
     * Lists all cliente entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Cliente', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Cliente')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );
        
        return $this->render('cliente/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new cliente entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Cliente', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $cliente = new Cliente();
        $form = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $cliente_existente = $em->getRepository('AppBundle:Cliente')->findBy(Array('documentoNumero' => $cliente->getDocumentoNumero(), 'activo' => 1));

            if (count($cliente_existente) > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un cliente con el número de documento ingresado.');
                return $this->render('cliente/new.html.twig', array(
                    'cliente' => $cliente,
                    'form' => $form->createView(),
                ));
            }
            
            $cliente->setSaldo(0);
            $cliente->setActivo(true);
            $cliente->setCreatedBy($this->getUser());
            $cliente->setCreatedAt(new \DateTime("now"));
            $cliente->setUpdatedBy($this->getUser());
            $cliente->setUpdatedAt(new \DateTime("now"));

            $em->persist($cliente);
            $em->flush();

            return $this->redirectToRoute('cliente_show', array('id' => $cliente->getId()));
        }

        return $this->render('cliente/new.html.twig', array(
            'cliente' => $cliente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cliente entity.
     *
     */
    public function showAction(Cliente $cliente)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Cliente', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('cliente'=>$cliente, 'movimiento'=>'Venta', 'activo' => 1));

        $recibos = $em->getRepository('AppBundle:Recibo')->findBy(Array('cliente'=>$cliente, 'activo' => 1));

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findBy(array('activo'=>1, 'cliente' => $cliente));

        $ordenesTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->findBy(array('activo'=>1, 'cliente' => $cliente));

        //Buscar todos los presupuestos del cliente seleccionado
        $presupuestoRepository = $em->getRepository(Presupuesto::class);
        $presupuestos = $presupuestoRepository->findBy(['cliente' => $cliente]);
        
        $deleteForm = $this->createDeleteForm($cliente);

        return $this->render('cliente/show.html.twig', array(
            'cliente' => $cliente,
            'comprobantes' => $comprobantes,
            'recibos' => $recibos,
            'ordenesTrabajo' => $ordenesTrabajo,
            'ordenesTrabajoContactologia' => $ordenesTrabajoContactologia,
            'presupuestos' => $presupuestos,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cliente entity.
     *
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Cliente', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($cliente);
        $editForm = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $cliente_existente = $em->getRepository('AppBundle:Cliente')->findBy(Array('documentoNumero' => $cliente->getDocumentoNumero(), 'activo' => 1));

            if(count($cliente_existente) == 1 && $cliente_existente[0]->getId() != $cliente->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un cliente con el número de documento ingresado.');
                return $this->render('cliente/edit.html.twig', array(
                    'cliente' => $cliente,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

            $cliente->setUpdatedBy($this->getUser());
            $cliente->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('cliente_index');
        }

        return $this->render('cliente/edit.html.twig', array(
            'cliente' => $cliente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cliente entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Cliente', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $cliente = $em->getRepository('AppBundle:Cliente')->find($id);
        if ($cliente->getActivo() > 0)
            $cliente->setActivo(0);
        else
            $cliente->setActivo(1);  

        $cliente->setUpdatedBy($this->getUser()); 
        $cliente->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush();
        
        return $this->redirectToRoute('cliente_index');
    }

    /**
     * Creates a form to delete a cliente entity.
     *
     * @param Cliente $cliente The cliente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cliente $cliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', array('id' => $cliente->getId())))
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
                        FROM AppBundle:Cliente r
                        WHERE (lower(r.nombre) LIKE :text_search OR r.documentoNumero LIKE :text_search)
                        AND r.activo = 1
                        ORDER BY r.nombre ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }

    public function findAction(Request $req) {
        //No se bien porque pero en producción siempre pincha esta función, por exceder la memoria

        $em = $this->getDoctrine()->getManager();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, array('json' => new JsonEncoder()));
        
        $cliente = $em->getRepository('AppBundle:Cliente')->find($req->get('clienteId'));

        $j_cliente = $serializer->serialize($cliente, 'json');

        return JsonResponse::create(array('cliente' => $j_cliente));
    }

    public function findObraSocialPlanAction(Request $req) {
        $em = $this->getDoctrine()->getManager();
        $cliente_obra_social_plan_id = $em->getRepository('AppBundle:Cliente')->find($req->get('clienteId'))->getObraSocialPlan()->getId();

        return JsonResponse::create(array('obra_social_plan_id' => $cliente_obra_social_plan_id));
    }
}
