<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;


/**
 * Usuario controller.
 *
 */
class UsuarioController extends AppController
{
    /**
     * Lists all usuario entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Usuario', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Usuario')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );
        
        return $this->render('usuario/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new usuario entity.
     *
     */
    public function newAction(Request $request)
    {

        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Usuario', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $usuario = new Usuario();
        $form = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $usuario_existente = $em->getRepository('AppBundle:Usuario')->findBy(Array('usuario' => $usuario->getUsuario(), 'activo' => 1));

            if(count($usuario_existente) > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un usuario con el nombre de usuario ingresado.');
                return $this->render('usuario/new.html.twig', array(
                    'usuario' => $usuario,
                    'form' => $form->createView(),
                ));
            }

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($password);
            
            $usuario->setLoginUltimo(new \DateTime("now"));
            $usuario->setLoginCantidad(0);
            $usuario->setActivo(true);
            $usuario->setCreatedBy($this->getUser());
            $usuario->setCreatedAt(new \DateTime("now"));
            $usuario->setUpdatedBy($this->getUser());
            $usuario->setUpdatedAt(new \DateTime("now"));

            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('usuario_show', array('id' => $usuario->getId()));
        }

        return $this->render('usuario/new.html.twig', array(
            'usuario' => $usuario,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a usuario entity.
     *
     */
    public function showAction(Usuario $usuario)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Usuario', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($usuario);

        return $this->render('usuario/show.html.twig', array(
            'usuario' => $usuario,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing usuario entity.
     *
     */
    public function editAction(Request $request, Usuario $usuario)
    {

        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Usuario', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($usuario);
        $editForm = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $usuario_existente = $em->getRepository('AppBundle:Usuario')->findBy(Array('usuario' => $usuario->getUsuario(), 'activo' => 1));

            if(count($usuario_existente) == 1 && $usuario_existente[0]->getId() != $usuario->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un usuario con el nombre de usuario ingresado.');
                return $this->render('usuario/edit.html.twig', array(
                    'usuario' => $usuario,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($password);
            
            $usuario->setUpdatedBy($this->getUser());
            $usuario->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('usuario_index');
        }

        return $this->render('usuario/edit.html.twig', array(
            'usuario' => $usuario,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a usuario entity.
     *
     */
    public function deleteAction($id)

    /**
     * Creates a form to delete a usuario entity.
     *
     * @param Usuario $usuario The usuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Usuario $usuario)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $usuario->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**   
    public function perfilAction($id)
    {
        // implement your own logic to retrieve file using $fileId
        $folder = $this->container->getParameter('dir_upload');
        
        $file = $folder.'/profile_pictures/'.$id.'.jpg';
       
        if (!is_file($file)) $file = new File($folder.'/profile_pictures/sin_foto.jpg');
        
        return $this->printAttach($file);
    }

    private function printAttach($file)
    {
        $file = new File($file);

        $filename = basename($file);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($file){
            $handle = fopen($file->getRealPath(), 'rb');
            while (!feof($handle)) {
                $buffer = fread($handle, 1024);
                echo $buffer;
                flush();
            }
            fclose($handle);
        });
        $d = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
        $response->headers->set('Content-Disposition', $d);
        $response->headers->set('Content-Type', $file->getMimeType());

        return $response;
    }
    */
}
