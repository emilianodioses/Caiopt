<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LibroCajaDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\LibroCaja;

/**
 * Librocajadetalle controller.
 *
 */
class LibroCajaDetalleController extends Controller
{
    /**
     * Lists all libroCajaDetalle entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $libroCajaDetalles = $em->getRepository('AppBundle:LibroCajaDetalle')->findAll();

        return $this->render('librocajadetalle/index.html.twig', array(
            'libroCajaDetalles' => $libroCajaDetalles,
        ));
    }

    /**
     * Creates a new libroCajaDetalle entity.
     *
     */
    public function newAction(Request $request, LibroCaja $libroCaja)
    {
        $libroCajaDetalle = new Librocajadetalle();
        $libroCajaDetalle->setLibroCaja($libroCaja);
        $libroCajaDetalle->setOrigen('Manual');
        $form = $this->createForm('AppBundle\Form\LibroCajaDetalleType', $libroCajaDetalle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $libroCajaDetalle->setActivo(true);
            $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
            $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

            $saldo = $libroCaja->getSaldoFinal();
            if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                $saldo += $libroCajaDetalle->getImporte();
            }
            else {
                $saldo -= $libroCajaDetalle->getImporte();
            }
            if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $libroCaja->setSaldoFinal($saldo);
            }

            $em->persist($libroCajaDetalle);
            $em->flush();

            return $this->redirectToRoute('librocaja_show', array('id' => $libroCaja->getId()));
        }

        return $this->render('librocajadetalle/new.html.twig', array(
            'libroCajaDetalle' => $libroCajaDetalle,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a libroCajaDetalle entity.
     *
     */
    public function showAction(LibroCajaDetalle $libroCajaDetalle)
    {
        $deleteForm = $this->createDeleteForm($libroCajaDetalle);

        return $this->render('librocajadetalle/show.html.twig', array(
            'libroCajaDetalle' => $libroCajaDetalle,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing libroCajaDetalle entity.
     *
     */
    public function editAction(Request $request, LibroCajaDetalle $libroCajaDetalle)
    {
        $tipo_anterior = $libroCajaDetalle->getTipo();
        $importe_anterior = $libroCajaDetalle->getImporte();
        $pago_tipo_anterior = $libroCajaDetalle->getPagoTipo()->getNombre();

        $deleteForm = $this->createDeleteForm($libroCajaDetalle);
        $editForm = $this->createForm('AppBundle\Form\LibroCajaDetalleType', $libroCajaDetalle);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

            $libroCaja = $libroCajaDetalle->getLibroCaja();
            $saldo = $libroCaja->getSaldoFinal();

            if ($pago_tipo_anterior == $libroCajaDetalle->getPagoTipo()->getNombre()) {
                //No cambia el tipo de pago
                if ($tipo_anterior == $libroCajaDetalle->getTipo()) {
                    if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                        $saldo += $libroCajaDetalle->getImporte() - $importe_anterior;
                    }
                    else {
                        $saldo -= $libroCajaDetalle->getImporte() - $importe_anterior;
                    }    
                }
                else {
                    if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                        $saldo += $importe_anterior + $libroCajaDetalle->getImporte();
                    }
                    else {
                        $saldo -= $importe_anterior + $libroCajaDetalle->getImporte();
                    }
                }

                if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                    $libroCaja->setSaldoFinal($saldo);
                }
            }
            elseif ($pago_tipo_anterior == ' Efectivo') {
                //El pago anterior era efectivo entonces hay q restar el importe anterior    
                if ($tipo_anterior == 'Ingreso a Caja') {
                    $saldo -= $importe_anterior;
                }
                else {
                    $saldo += $importe_anterior;
                }    

                $libroCaja->setSaldoFinal($saldo);
            }
            elseif ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                //El pago actual es efectivo entonces hay q sumar el importe anterior
                if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                    $saldo += $libroCajaDetalle->getImporte();
                }
                else {
                    $saldo -= $libroCajaDetalle->getImporte();
                }    

                $libroCaja->setSaldoFinal($saldo);
            }

            $em->flush();

            return $this->redirectToRoute('librocaja_show', array('id' => $libroCaja->getId()));
        }

        return $this->render('librocajadetalle/edit.html.twig', array(
            'libroCajaDetalle' => $libroCajaDetalle,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a libroCajaDetalle entity.
     *
     */
    public function deleteAction(Request $request, LibroCajaDetalle $libroCajaDetalle)
    {
        $em = $this->getDoctrine()->getManager();

        //$libroCajaDetalle = $em->getRepository('AppBundle:LibroCajaDetalle')->find($id);
        
        $libroCajaDetalle->setActivo(false);
        $libroCajaDetalle->setUpdatedBy($this->getUser()->getId()); 
        $libroCajaDetalle->setUpdatedAt(new \DateTime("now")); 

        $libroCaja = $libroCajaDetalle->getLibroCaja();
        $saldo = $libroCaja->getSaldoFinal();
        
        if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
            $saldo -= $libroCajaDetalle->getImporte();
        }
        else {
            $saldo += $libroCajaDetalle->getImporte();
        }    
        
        if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
            $libroCaja->setSaldoFinal($saldo);
        }
        
        $em->flush();

        return $this->redirectToRoute('librocaja_show', array('id' => $libroCaja->getId()));
    }

    /**
     * Creates a form to delete a libroCajaDetalle entity.
     *
     * @param LibroCajaDetalle $libroCajaDetalle The libroCajaDetalle entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LibroCajaDetalle $libroCajaDetalle)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('librocajadetalle_delete', array('id' => $libroCajaDetalle->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
