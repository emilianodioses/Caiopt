<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Medico;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Medico controller.
 *
 */
class MedicoController extends Controller
{
    /**
     * Lists all medico entities.
     *
     */
    public function indexAction(Request $request)
    {

        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Medico', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Medico')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('medico/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new medico entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Medico', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */
        
        $medico = new Medico();
        $form = $this->createForm('AppBundle\Form\MedicoType', $medico);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (is_null($medico->getDocumentoNumero())) {
                $medico->setDocumentoNumero('');
            }

            $medico->setActivo(true);
            $medico->setCreatedBy($this->getUser());
            $medico->setCreatedAt(new \DateTime("now"));
            $medico->setUpdatedBy($this->getUser());
            $medico->setUpdatedAt(new \DateTime("now"));
            
            $em->persist($medico);
            $em->flush();

            return $this->redirectToRoute('medico_show', array('id' => $medico->getId()));
        }

        return $this->render('medico/new.html.twig', array(
            'medico' => $medico,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a medico entity.
     *
     */
    public function showAction(Medico $medico)
    {

        $em = $this->getDoctrine()->getManager();

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findBy(array('activo'=>1, 'medico' => $medico));

        $ordenesTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->findBy(array('activo'=>1, 'medico' => $medico));

        $deleteForm = $this->createDeleteForm($medico);

        return $this->render('medico/show.html.twig', array(
            'medico' => $medico,
            'ordenesTrabajo' => $ordenesTrabajo,
            'ordenesTrabajoContactologia' => $ordenesTrabajoContactologia,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing medico entity.
     *
     */
    public function editAction(Request $request, Medico $medico)
    {
        $deleteForm = $this->createDeleteForm($medico);
        $editForm = $this->createForm('AppBundle\Form\MedicoType', $medico);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (is_null($medico->getDocumentoNumero())) {
                $medico->setDocumentoNumero('');
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('medico_show', array('id' => $medico->getId()));
        }

        return $this->render('medico/edit.html.twig', array(
            'medico' => $medico,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a medico entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $medico = $em->getRepository('AppBundle:Medico')->find($id);

        if ($medico->getActivo() > 0)
            $medico->setActivo(0);
        else
            $medico->setActivo(1); 
        
        $medico->setUpdatedBy($this->getUser()); 
        $medico->setUpdatedAt(new \DateTime("now")); 

        $em->flush();

        return $this->redirectToRoute('medico_index');
    }

    /**
     * Creates a form to delete a medico entity.
     *
     * @param Medico $medico The medico entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Medico $medico)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('medico_delete', array('id' => $medico->getId())))
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
                        SELECT r.id as id, CONCAT(r.nombre, \' (\', r.matricula, \')\')  as text
                        FROM AppBundle:Medico r
                        WHERE (lower(r.nombre) LIKE :text_search OR r.matricula LIKE :text_search)
                        AND r.activo = 1
                        ORDER BY r.nombre ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }
}
