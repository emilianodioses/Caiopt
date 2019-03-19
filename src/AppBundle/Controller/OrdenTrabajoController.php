<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajo;
use AppBundle\Entity\OrdenTrabajoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\OrdenTrabajoType;

/**
 * Ordentrabajo controller.
 *
 */
class OrdenTrabajoController extends Controller
{
    /**
     * Lists all ordenTrabajo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findBy(array('activo'=>1));

        return $this->render('ordentrabajo/index.html.twig', array(
            'ordenesTrabajo' => $ordenesTrabajo,
        ));
    }

    /**
     * Creates a new ordenTrabajo entity.
     *
     */
    public function newAction(Request $request)
    {
        $ordenTrabajo = new Ordentrabajo();
        $form = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ordenTrabajo->setActivo(1);
            $ordenTrabajo->setCreatedBy($this->getUser()->getId());
            $ordenTrabajo->setCreatedAt(new \DateTime("now"));
            $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenTrabajo);

            $ordentrabajodetalle = new OrdenTrabajoDetalle();
            $ordentrabajodetalles  = $ordenTrabajo->getOrdenTrabajoDetalles()->toArray();

            foreach($ordentrabajodetalles as $ordentrabajodetalle) {
                $ordentrabajodetalle->setOrdenTrabajo($ordenTrabajo);
                $ordentrabajodetalle->setActivo(1);
                $ordentrabajodetalle->setCreatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajodetalle->setUpdatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajodetalle);
            }

            $em->flush();

            return $this->redirectToRoute('ordentrabajo_show', array('id' => $ordenTrabajo->getId()));
        }

        return $this->render('ordentrabajo/new.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ordenTrabajo entity.
     *
     */
    public function showAction(OrdenTrabajo $ordenTrabajo)
    {
        $em = $this->getDoctrine()->getManager();
        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($ordenTrabajo);

        return $this->render('ordentrabajo/show.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'ordentrabajodetalles' => $ordentrabajodetalles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenTrabajo entity.
     *
     */
    public function editAction(Request $request, OrdenTrabajo $ordenTrabajo)
    {

        $em = $this->getDoctrine()->getManager();

        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        foreach($ordentrabajodetalles as $ordentrabajodetalle) {
            $ordenTrabajo->getOrdenTrabajoDetalles()->add($ordentrabajodetalle);
        }

        $deleteForm = $this->createDeleteForm($ordenTrabajo);
        $editForm = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));
            $em->persist($ordenTrabajo);
            $em->flush();

            return $this->redirectToRoute('ordentrabajo_show', array('id' => $ordenTrabajo->getId()));
        }

        return $this->render('ordentrabajo/edit.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ordenTrabajo entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($id);
        if ($ordenTrabajo->getActivo() > 0)
            $ordenTrabajo->setActivo(0);
        else
            $ordenTrabajo->setActivo(1);  

        $ordenTrabajo->setUpdatedBy($this->getUser()->getId()); 
        $ordenTrabajo->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($ordenTrabajo);
        
        return $this->redirectToRoute('ordentrabajo_index');
    }

    /**
     * Creates a form to delete a ordenTrabajo entity.
     *
     * @param OrdenTrabajo $ordenTrabajo The ordenTrabajo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenTrabajo $ordenTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordentrabajo_delete', array('id' => $ordenTrabajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
