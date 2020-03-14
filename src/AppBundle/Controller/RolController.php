<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Rol;
use AppBundle\Entity\RolFuncion;
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
            15/*limit per page*/
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

        $query = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
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

        $funcionesAsignados = $em->getRepository('AppBundle:Rol')->findByAsignado($rol->getId())->getResult();
        $funcionesNoAsignados = $em->getRepository('AppBundle:Rol')->findByNoAsignado($rol->getId())->getResult();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $rol->setActivo(true);
            $rol->setCreatedBy($this->getUser());
            $rol->setCreatedAt(new \DateTime("now"));
            $rol->setUpdatedBy($this->getUser());
            $rol->setUpdatedAt(new \DateTime("now"));

            $em->persist($rol);
            $em->flush();

            $permisos_id_array = stripcslashes($request->get('permisos'));
            $permisos_id_array = json_decode($permisos_id_array,TRUE);

            $permisosActuales = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));

            foreach($permisosActuales as $permiso) {
                $em->remove($permiso);
            }

            foreach($permisos_id_array as $permiso) {
                $rolFuncion = new RolFuncion();

                $rolActual = $em->getRepository('AppBundle:Rol')->find($rol->getId());
                $funcionActual = $em->getRepository('AppBundle:Funcion')->find($permiso['id']);
                $rolFuncion->setRol($rolActual);
                $rolFuncion->setFuncion($funcionActual);
                $em->persist($rolFuncion);
            }

            $em->flush();

            return $this->redirectToRoute('rol_show', array('id' => $rol->getId()));
        }

        return $this->render('rol/new.html.twig', array(
            'rol' => $rol,
            'funcionesAsignados' => $funcionesAsignados,
            'funcionesNoAsignados' => $funcionesNoAsignados,
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

            $permisosActuales = $em->getRepository('AppBundle:RolFuncion')->findBy(array('rol' => $rol));

            
            foreach($permisosActuales as $permiso) {
                $em->remove($permiso);
            }

            foreach($permisos_id_array as $permiso) {
                $rolFuncion = new RolFuncion();

                $rolActual = $em->getRepository('AppBundle:Rol')->find($rol->getId());
                $funcionActual = $em->getRepository('AppBundle:Funcion')->find($permiso['id']);
                $rolFuncion->setRol($rolActual);
                $rolFuncion->setFuncion($funcionActual);
                $em->persist($rolFuncion);
            }
    

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
        
        $em->flush();
        
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
}
