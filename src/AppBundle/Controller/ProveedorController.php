<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proveedor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Proveedor controller.
 *
 */
class ProveedorController extends Controller
{
    /**
     * Lists all proveedor entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $proveedors = $em->getRepository('AppBundle:Proveedor')->findBy(array('activo'=> true));

        return $this->render('proveedor/index.html.twig', array(
            'proveedors' => $proveedors,
        ));
    }

    /**
     * Creates a new proveedor entity.
     *
     */
    public function newAction(Request $request)
    {
        $proveedor = new Proveedor();
        $form = $this->createForm('AppBundle\Form\ProveedorType', $proveedor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $proveedor->setActivo(true);
            $proveedor->setCreatedBy($this->getUser()->getId());
            $proveedor->setCreatedAt(new \DateTime("now"));
            $proveedor->setUpdatedBy($this->getUser()->getId());
            $proveedor->setUpdatedAt(new \DateTime("now"));

            $em->persist($proveedor);
            $em->flush();

            return $this->redirectToRoute('proveedor_show', array('id' => $proveedor->getId()));
        }

        return $this->render('proveedor/new.html.twig', array(
            'proveedor' => $proveedor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a proveedor entity.
     *
     */
    public function showAction(Proveedor $proveedor)
    {
        $deleteForm = $this->createDeleteForm($proveedor);

        return $this->render('proveedor/show.html.twig', array(
            'proveedor' => $proveedor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing proveedor entity.
     *
     */
    public function editAction(Request $request, Proveedor $proveedor)
    {
        $deleteForm = $this->createDeleteForm($proveedor);
        $editForm = $this->createForm('AppBundle\Form\ProveedorType', $proveedor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $proveedor->setUpdatedBy($this->getUser()->getId());
            $proveedor->setUpdatedAt(new \DateTime("now"));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proveedor_index');
        }

        return $this->render('proveedor/edit.html.twig', array(
            'proveedor' => $proveedor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a proveedor entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $proveedor = $em->getRepository('AppBundle:Proveedor')->find($id);
        if ($proveedor->getActivo() > 0)
            $proveedor->setActivo(0);
        else
            $proveedor->setActivo(1);  
        
        $proveedor->setUpdatedBy($this->getUser()->getId());
        $proveedor->setUpdatedAt(new \DateTime("now"));

        $em->flush($proveedor);

        return $this->redirectToRoute('proveedor_index');
    }

    /**
     * Creates a form to delete a proveedor entity.
     *
     * @param Proveedor $proveedor The proveedor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Proveedor $proveedor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('proveedor_delete', array('id' => $proveedor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
