<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        
        return $this->render('usuario/index.html.twig', array(
            'usuarios' => $usuarios,
        ));
    }

    /**
     * Creates a new usuario entity.
     *
     */
    public function newAction(Request $request)
    {
        $usuario = new Usuario();
        $form = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($password);
            
            $usuario->setLoginUltimo(new \DateTime("now"));
            $usuario->setLoginCantidad(0);
            $usuario->setActivo(true);
            $usuario->setCreatedBy($this->getUser()->getId());
            $usuario->setCreatedAt(new \DateTime("now"));
            $usuario->setUpdatedBy($this->getUser()->getId());
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
        $deleteForm = $this->createDeleteForm($usuario);
        $editForm = $this->createForm('AppBundle\Form\UsuarioType', $usuario);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
            $usuario->setPassword($password);
            
            $usuario->setUpdatedBy($this->getUser()->getId());
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
     * Change the User status to active or inactive
     *
     */
    public function statusAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AppBundle:Usuario')->find($id);
        if ($usuario->getActivo() > 0)
            $usuario->setActivo(0);
        else
            $usuario->setActivo(1);  
        
        $em->flush($usuario);

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        
        return $this->render('usuario/index.html.twig', array(
            'usuarios' => $usuarios,
        ));
    }



    /**
     * Deletes a usuario entity.
     *
     */
    public function deleteAction(Request $request, Usuario $usuario)
    {
        $form = $this->createDeleteForm($usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($usuario);
            $em->flush();
        }

        return $this->redirectToRoute('usuario_index');
    }

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
}
