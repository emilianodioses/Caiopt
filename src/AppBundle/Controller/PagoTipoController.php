<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PagoTipo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Pagotipo controller.
 *
 */
class PagoTipoController extends Controller
{
    /**
     * Lists all pagoTipo entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PagoTipo', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $pagoTipos = $em->getRepository('AppBundle:PagoTipo')->findAll();

        return $this->render('pagotipo/index.html.twig', array(
            'pagoTipos' => $pagoTipos,
        ));
    }

    /**
     * Creates a new pagoTipo entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PagoTipo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $pagoTipo = new Pagotipo();
        $form = $this->createForm('AppBundle\Form\PagoTipoType', $pagoTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pagoTipo);
            $em->flush();

            return $this->redirectToRoute('pagotipo_show', array('id' => $pagoTipo->getId()));
        }

        return $this->render('pagotipo/new.html.twig', array(
            'pagoTipo' => $pagoTipo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pagoTipo entity.
     *
     */
    public function showAction(PagoTipo $pagoTipo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PagoTipo', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($pagoTipo);

        return $this->render('pagotipo/show.html.twig', array(
            'pagoTipo' => $pagoTipo,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pagoTipo entity.
     *
     */
    public function editAction(Request $request, PagoTipo $pagoTipo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PagoTipo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($pagoTipo);
        $editForm = $this->createForm('AppBundle\Form\PagoTipoType', $pagoTipo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pagotipo_edit', array('id' => $pagoTipo->getId()));
        }

        return $this->render('pagotipo/edit.html.twig', array(
            'pagoTipo' => $pagoTipo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pagoTipo entity.
     *
     */
    public function deleteAction(Request $request, PagoTipo $pagoTipo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('PagoTipo', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $form = $this->createDeleteForm($pagoTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pagoTipo);
            $em->flush();
        }

        return $this->redirectToRoute('pagotipo_index');
    }

    /**
     * Creates a form to delete a pagoTipo entity.
     *
     * @param PagoTipo $pagoTipo The pagoTipo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(PagoTipo $pagoTipo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pagotipo_delete', array('id' => $pagoTipo->getId())))
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
                        SELECT r.id as id, r.nombre as text
                        FROM AppBundle:PagoTipo r
                        WHERE lower(r.nombre) LIKE :text_search
                        ORDER BY r.nombre ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }
}
