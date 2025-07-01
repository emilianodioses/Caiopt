<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sucursal;
use AppBundle\Entity\Stock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Sucursal controller.
 *
 */
class SucursalController extends Controller
{
    /**
     * Lists all sucursal entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Sucursal', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Sucursal')->findByTexto($texto);


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('sucursal/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new sucursal entity.
     *
     */
    public function newAction(Request $request)
    {
        $sucursal = new Sucursal();
        $form = $this->createForm('AppBundle\Form\SucursalType', $sucursal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sucursal->setActivo(true);
            $sucursal->setCreatedBy($this->getUser());
            $sucursal->setCreatedAt(new \DateTime("now"));
            $sucursal->setUpdatedBy($this->getUser());
            $sucursal->setUpdatedAt(new \DateTime("now"));
            
            $em->persist($sucursal);

            $articulos = $em->getRepository('AppBundle:Articulo')->findBy(array('activo' => true));

            foreach ($articulos as $articulo) {
                $stock = new Stock();

                $stock->setArticulo($articulo);
                $stock->setSucursal($sucursal);
                $stock->setCantidad(0);
                $stock->setCantidadMinima(1);
                $stock->setActivo(true);
                $stock->setCreatedBy($this->getUser());
                $stock->setCreatedAt(new \DateTime("now"));
                $stock->setUpdatedBy($this->getUser());
                $stock->setUpdatedAt(new \DateTime("now"));

                $em->persist($stock);
            }

            $em->flush();

            return $this->redirectToRoute('sucursal_show', array('id' => $sucursal->getId()));
        }

        return $this->render('sucursal/new.html.twig', array(
            'sucursal' => $sucursal,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sucursal entity.
     *
     */
    public function showAction(Sucursal $sucursal)
    {
        $deleteForm = $this->createDeleteForm($sucursal);

        return $this->render('sucursal/show.html.twig', array(
            'sucursal' => $sucursal,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sucursal entity.
     *
     */
    public function editAction(Request $request, Sucursal $sucursal)
    {
        $deleteForm = $this->createDeleteForm($sucursal);
        $editForm = $this->createForm('AppBundle\Form\SucursalType', $sucursal);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sucursal_show', array('id' => $sucursal->getId()));
        }

        return $this->render('sucursal/edit.html.twig', array(
            'sucursal' => $sucursal,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sucursal entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $sucursal = $em->getRepository('AppBundle:Sucursal')->find($id);

        if ($sucursal->getActivo() > 0)
            $sucursal->setActivo(0);
        else
            $sucursal->setActivo(1); 
        
        $sucursal->setUpdatedBy($this->getUser()); 
        $sucursal->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('sucursal_index');
    }

    /**
     * Creates a form to delete a sucursal entity.
     *
     * @param Sucursal $sucursal The sucursal entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Sucursal $sucursal)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sucursal_delete', array('id' => $sucursal->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function elegirSucursalAction(Request $request)
    {       
        $sucursalId = $request->request->get('sucursalId');
        
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AppBundle:Usuario')->find($this->getUser()->getId());

        $sucursal = $em->getRepository('AppBundle:Sucursal')->find($sucursalId);       
        
        $usuario->setSucursal($sucursal);
        
        $em->flush();

        $mensaje = $sucursalId;
        
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));       
        $j_mensaje = $serializer->serialize((int)$mensaje, 'json');
        return JsonResponse::create(array('mensaje' => $j_mensaje));
    }
}
