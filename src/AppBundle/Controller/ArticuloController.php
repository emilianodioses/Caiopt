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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articulos = $em->getRepository('AppBundle:Articulo')->findAll();

        return $this->render('articulo/index.html.twig', array(
            'articulos' => $articulos,
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

            return $this->redirectToRoute('articulo_edit', array('id' => $articulo->getId()));
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
    public function deleteAction(Request $request, Articulo $articulo)
    {
        $form = $this->createDeleteForm($articulo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($articulo);
            $em->flush();
        }

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
        
        return JsonResponse::create(array('articulo' => $j_articulo
                ));
    }
}