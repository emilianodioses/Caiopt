<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parametro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Parametro controller.
 *
 */
class ParametroController extends Controller
{
    /**
     * Lists all parametro entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

//        if (!$secure->isAuthorized('Parametro', 'Index', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Parametro')->findByTexto($texto);


        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('Parametro/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new Parametro entity.
     *
     */
    public function newAction(Request $request)
    {
        $parametro = new Parametro();
        $form = $this->createForm('AppBundle\Form\ParametroType', $parametro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $parametro->setActivo(true);
//            $parametro->setCreatedBy($this->getUser());
            $parametro->setCreatedAt(new \DateTime("now"));
//            $parametro->setUpdatedBy($this->getUser());
            $parametro->setUpdatedAt(new \DateTime("now"));

            $em->persist($parametro);
            $em->flush();

            return $this->redirectToRoute('parametro_show', array('id' => $parametro->getId()));
        }

        return $this->render('parametro/new.html.twig', array(
            'parametro' => $parametro,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parametro entity.
     *
     */
    public function showAction(Parametro $parametro)
    {
        $deleteForm = $this->createDeleteForm($parametro);

        return $this->render('parametro/show.html.twig', array(
            'parametro' => $parametro,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parametro entity.
     *
     */
    public function editAction(Request $request, Parametro $parametro)
    {
        $deleteForm = $this->createDeleteForm($parametro);
        $editForm = $this->createForm('AppBundle\Form\ParametroType', $parametro);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parametro_show', array('id' => $parametro->getId()));
        }

        return $this->render('parametro/edit.html.twig', array(
            'parametro' => $parametro,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parametro entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $parametro = $em->getRepository('AppBundle:Parametro')->find($id);

        if ($parametro->getActivo() > 0)
            $parametro->setActivo(0);
        else
            $parametro->setActivo(1);

        $parametro->setUpdatedBy($this->getUser());
        $parametro->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('parametro_index');
    }

    /**
     * Creates a form to delete a parametro entity.
     *
     * @param Parametro $parametro The parametro entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parametro $parametro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametro_delete', array('id' => $parametro->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

//    public function elegirParametroAction(Request $request)
//    {
//        $parametroId = $request->request->get('parametroId');
//
//        $em = $this->getDoctrine()->getManager();
//
//        $usuario = $em->getRepository('AppBundle:Usuario')->find($this->getUser()->getId());
//
//        $parametro = $em->getRepository('AppBundle:Parametro')->find($parametroId);
//
//        $usuario->setParametro($parametro);
//
//        $em->flush();
//
//        $mensaje = $parametroId;
//
//        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
//        $j_mensaje = $serializer->serialize((int)$mensaje, 'json');
//        return JsonResponse::create(array('mensaje' => $j_mensaje));
//    }
}
