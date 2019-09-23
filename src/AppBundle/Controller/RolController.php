<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rol;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * Rol controller.
 *
 */
class RolController extends Controller
{
    /**
     * Lists all rol entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Rol', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        //$texto = $request->get('texto','');

        //$query = $em->getRepository('AppBundle:Rol')->findByTexto($texto);

        $query = $em->getRepository('AppBundle:Rol')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('rol/index.html.twig', array(
            'pagination' => $pagination,
            //'texto' => $texto
        ));
    }

    /**
     * Finds and displays a rol entity.
     *
     */
    public function showAction(Rol $rol, Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Rol', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository('AppBundle:Rol')->findByRol($rol->getId());
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('rol/show.html.twig', array(
            'rol' => $rol,
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new rol entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Rol', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $rol = new Rol();
        $form = $this->createForm('AppBundle\Form\RolType', $rol);
        $form->handleRequest($request);

        $rolFunciones = $em->getRepository('AppBundle:RolFuncion')
                ->createQueryBuilder('rf')
                ->innerjoin('rf.funcion','f')
                ->getQuery() 
                ->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $rol->setActivo(true);
            $rol->setCreatedBy($this->getUser());
            $rol->setCreatedAt(new \DateTime("now"));
            $rol->setUpdatedBy($this->getUser());
            $rol->setUpdatedAt(new \DateTime("now"));

            $em->persist($rol);
            $em->flush();

            return $this->redirectToRoute('rol_show', array('id' => $rol->getId()));
        }

        return $this->render('rol/new.html.twig', array(
            'rol' => $rol,
            'rolFunciones' => $rolFunciones,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing rol entity.
     *
     */
    public function editAction(Request $request, Rol $rol)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Rol', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        
        $funcionesAsignados = $em->getRepository('AppBundle:Rol')->findByAsignado($rol->getId())->getResult();
        $funcionesNoAsignados = $em->getRepository('AppBundle:Rol')->findByNoAsignado($rol->getId())->getResult();

        foreach($funcionesAsignados as $funcion) {
            $rol->getFunciones()->add($funcion);
        }

        $deleteForm = $this->createDeleteForm($rol);
        $editForm = $this->createForm('AppBundle\Form\RolType', $rol);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $permisos_id_array = stripcslashes($request->get('permisos'));
            $permisos_id_array = json_decode($permisos_id_array,TRUE);

            dump($permisos_id_array);
            die;

            $rol->setUpdatedBy($this->getUser());
            $rol->setUpdatedAt(new \DateTime("now"));
            $em->flush();

            return $this->redirectToRoute('rol_index');
        }

        return $this->render('rol/edit.html.twig', array(
            'rol' => $rol,
            'funcionesAsignados' => $funcionesAsignados,
            'funcionesNoAsignados' => $funcionesNoAsignados,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
        
        if (!$secure->isAuthorized('Rol', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $rol = $em->getRepository('AppBundle:Rol')->find($id);
        if ($rol->getActivo() > 0)
            $rol->setActivo(0);
        else
            $rol->setActivo(1);  

        $rol->setUpdatedBy($this->getUser()); 
        $rol->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($rol);
        
        return $this->redirectToRoute('rol_index');
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

    public function updateAction(Request $request)
    {                
        $rol = $request->request->get('rol');
        $selectedItems = $request->request->get('selectedItems');

        $em = $this->getDoctrine()->getManager();
        
        $rol = $em->getRepository('AppBundle:Rol')->find($rol);
        $rol->setDescripcion($descripcion);

        $rolFuncion = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));          

        foreach($rolFuncion as $funcion) {
            $em->remove($funcion);
        }
        
        foreach($selectedItems as $funcion) {
            $em->persist($funcion);
        }

        $em->flush();

        return true;
    }
}
