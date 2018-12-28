<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Articulo controller.
 *
 */
class ArticuloController extends Controller
{
    /**
     * Lists all articulo entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Articulo')->findByTexto($texto);

        //$query = $em->getRepository('AppBundle:Articulo')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('articulo/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new articulo entity.
     *
     */
    public function newAction(Request $request)
    {
        $articulo = new Articulo();
        $form = $this->createForm('AppBundle\Form\ArticuloType', $articulo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $articulo->setCreatedBy($this->getUser()->getId());
            $articulo->setCreatedAt(new \DateTime("now"));
            $articulo->setUpdatedBy($this->getUser()->getId());
            $articulo->setUpdatedAt(new \DateTime("now"));

            $em->persist($articulo);
            $em->flush();

            return $this->redirectToRoute('articulo_show', array('id' => $articulo->getId()));
        }

        return $this->render('articulo/new.html.twig', array(
            'articulo' => $articulo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a articulo entity.
     *
     */
    public function showAction(Articulo $articulo)
    {
        $deleteForm = $this->createDeleteForm($articulo);

        return $this->render('articulo/show.html.twig', array(
            'articulo' => $articulo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing articulo entity.
     *
     */
    public function editAction(Request $request, Articulo $articulo)
    {
        $deleteForm = $this->createDeleteForm($articulo);
        $editForm = $this->createForm('AppBundle\Form\ArticuloType', $articulo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('articulo_show', array('id' => $articulo->getId()));
        }

        return $this->render('articulo/edit.html.twig', array(
            'articulo' => $articulo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a articulo entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $articulo = $em->getRepository('AppBundle:Articulo')->find($id);
        if ($articulo->getActivo() > 0)
            $articulo->setActivo(0);
        else
            $articulo->setActivo(1);  

        $articulo->setUpdatedBy($this->getUser()->getId()); 
        $articulo->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($articulo);
        
        return $this->redirectToRoute('articulo_index');
    }

    /**
     * Creates a form to delete a articulo entity.
     *
     * @param Articulo $articulo The articulo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Articulo $articulo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('articulo_delete', array('id' => $articulo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function findAction(Request $req) {
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
        $articulo = $this->getDoctrine()->getManager('default')->getRepository('AppBundle:Articulo')
                ->find($req->get('articuloId'));
        
        $j_articulo = $serializer->serialize($articulo, 'json');
        
        return JsonResponse::create(array('articulo' => $j_articulo));
    }

    public function findAllJsonAction(Request $request) {
        $em = $em = $this->getDoctrine()->getManager('default');
        
        $text_search = $request->get('q');
        $pageLimit = $request->get('page_limit');

        if (!is_numeric($pageLimit) || $pageLimit > 10) {
            $pageLimit = 10;
        }

        $result = $em->createQuery('
                        SELECT r.id as id, r.descripcion as text
                        FROM AppBundle:Articulo r
                        WHERE lower(r.descripcion) LIKE :text_search
                        ORDER BY r.descripcion ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }
}