<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recibo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ReciboComprobante;
use AppBundle\Entity\Cliente;

/**
 * Recibo controller.
 *
 */
class ReciboController extends Controller
{
    /**
     * Lists all recibo entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $recibos = $em->getRepository('AppBundle:Recibo')->findBy(Array('activo' => 1));

        return $this->render('recibo/index.html.twig', array(
            'recibos' => $recibos,
        ));
    }

    /**
     * Creates a new recibo entity.
     *
     */
    public function newAction(Request $request, Comprobante $comprobante)
    {
        $recibo = new Recibo();
        $recibo->setFecha(new \DateTime("now"));

        $form = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $reciboComprobante = new ReciboComprobante();
        $reciboComprobante->setComprobante($comprobante);
        $reciboComprobante->setImporte($comprobante->getPendiente());
        
        
        $reciboComprobantes[] = $reciboComprobante;

        if ($form->isSubmitted() && $form->isValid()) {
            //Por el momento no permito dejar plata a cuenta
            if ($recibo->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('recibo_new', array('request' => $request, 'comprobante' => $comprobante->getId()));
            }

            $em = $this->getDoctrine()->getManager();

            $max_numero_recibo = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:Recibo', 'c')
             ->getQuery()
             ->getSingleScalarResult();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($recibo->getObservaciones())) {
                $recibo->setObservaciones('');
            }

            $recibo->setSucursal($sucursal);
            $recibo->setNumero($max_numero_recibo+1);
            $recibo->setSaldo(0);
            $recibo->setActivo(1);
            $recibo->setCreatedBy($this->getUser()->getId());
            $recibo->setCreatedAt(new \DateTime("now"));
            $recibo->setUpdatedBy($this->getUser()->getId());
            $recibo->setUpdatedAt(new \DateTime("now"));

            $em->persist($recibo);

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setCreatedBy($this->getUser()->getId());
                $clientePago->setCreatedAt(new \DateTime("now"));
                $clientePago->setUpdatedBy($this->getUser()->getId());
                $clientePago->setUpdatedAt(new \DateTime("now"));

                $em->persist($clientePago);
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            $disponible = $recibo->getTotal();
            foreach($reciboComprobantes as $reciboComprobante) {
                $comprobante = $reciboComprobante->getComprobante();
                if ($disponible >= $comprobante->getPendiente()) {
                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible -= $comprobante->getPendiente();
                }
                else {
                    $pendiente = $comprobante->getPendiente() - $disponible;
                    $importe = $disponible;
                    $disponible = 0;
                }

                $comprobante->setPendiente($pendiente);
                $comprobante->setUpdatedBy($this->getUser()->getId());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante->setRecibo($recibo);
                $reciboComprobante->setComprobante($comprobante);
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setActivo(1);
                $reciboComprobante->setCreatedBy($this->getUser()->getId());
                $reciboComprobante->setCreatedAt(new \DateTime("now"));
                $reciboComprobante->setUpdatedBy($this->getUser()->getId());
                $reciboComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($reciboComprobante);
            }

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }
        
        return $this->render('recibo/new.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
            'reciboComprobantes' => $reciboComprobantes,
        ));
    }

    /**
     * Creates a new recibo entity.
     *
     */
    public function clienteNewAction(Request $request, Cliente $cliente)
    {
        $em = $this->getDoctrine()->getManager();
        $recibo = new Recibo();
        $recibo->setFecha(new \DateTime("now"));
        $form = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $comprobantes = $em->createQuery('SELECT c
            FROM AppBundle:Comprobante c
            WHERE c.cliente = :cliente
            AND c.activo = 1
            AND c.movimiento = \'Venta\'
            AND c.pendiente > 0')
              ->setParameter('cliente', $cliente)
              ->getResult();
              
        foreach($comprobantes as $comprobante) {
            $reciboComprobante = new ReciboComprobante();
            $reciboComprobante->setComprobante($comprobante);
            $reciboComprobante->setImporte($comprobante->getPendiente());
            
            
            $reciboComprobantes[] = $reciboComprobante;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dump($request);
            echo($request->get('comprobantes'));
            die;
            //Por el momento no permito dejar plata a cuenta
            if ($recibo->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('recibo_cliente_new', array('request' => $request, 'cliente' => $cliente->getId()));
            }

            

            $max_numero_recibo = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:Recibo', 'c')
             ->getQuery()
             ->getSingleScalarResult();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($recibo->getObservaciones())) {
                $recibo->setObservaciones('');
            }

            $recibo->setSucursal($sucursal);
            $recibo->setNumero($max_numero_recibo+1);
            $recibo->setSaldo(0);
            $recibo->setActivo(1);
            $recibo->setCreatedBy($this->getUser()->getId());
            $recibo->setCreatedAt(new \DateTime("now"));
            $recibo->setUpdatedBy($this->getUser()->getId());
            $recibo->setUpdatedAt(new \DateTime("now"));

            $em->persist($recibo);

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setCreatedBy($this->getUser()->getId());
                $clientePago->setCreatedAt(new \DateTime("now"));
                $clientePago->setUpdatedBy($this->getUser()->getId());
                $clientePago->setUpdatedAt(new \DateTime("now"));

                $em->persist($clientePago);
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            $disponible = $recibo->getTotal();
            foreach($reciboComprobantes as $reciboComprobante) {
                $comprobante = $reciboComprobante->getComprobante();
                if ($disponible >= $comprobante->getPendiente()) {
                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible -= $comprobante->getPendiente();
                }
                else {
                    $pendiente = $comprobante->getPendiente() - $disponible;
                    $importe = $disponible;
                    $disponible = 0;
                }

                $comprobante->setPendiente($pendiente);
                $comprobante->setUpdatedBy($this->getUser()->getId());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante->setRecibo($recibo);
                $reciboComprobante->setComprobante($comprobante);
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setActivo(1);
                $reciboComprobante->setCreatedBy($this->getUser()->getId());
                $reciboComprobante->setCreatedAt(new \DateTime("now"));
                $reciboComprobante->setUpdatedBy($this->getUser()->getId());
                $reciboComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($reciboComprobante);
            }

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }
        
        return $this->render('recibo/new.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
            'reciboComprobantes' => $reciboComprobantes,
        ));
    }

    /**
     * Finds and displays a recibo entity.
     *
     */
    public function showAction(Recibo $recibo)
    {
        $em = $this->getDoctrine()->getManager();
        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo'=>$recibo,  'activo'=>1));
        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($recibo);

        return $this->render('recibo/show.html.twig', array(
            'recibo' => $recibo,
            'clientePagos' => $clientePagos,
            'reciboComprobantes' => $reciboComprobantes,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing recibo entity.
     *
     */
    public function editAction(Request $request, Recibo $recibo)
    {
        $em = $this->getDoctrine()->getManager();

        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        foreach($clientePagos as $clientePago) {
            $recibo->getClientePagos()->add($clientePago);
        }

        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($recibo);
        $editForm = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //Por el momento no permito dejar plata a cuenta
            if ($recibo->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('recibo_edit', array('request' => $request, 'id' => $recibo->getId()));
            }

            $em = $this->getDoctrine()->getManager();

            if (is_null($recibo->getObservaciones())) {
                $recibo->setObservaciones('');
            }

            $recibo->setSaldo(0);
            $recibo->setUpdatedBy($this->getUser()->getId());
            $recibo->setUpdatedAt(new \DateTime("now"));

            //**********************************************************************
            //ESTA parte es para que funcione el delete de pagos.
            //Basicamente seteo a todos los articulos ya existen en la base de datos con 
            //Activo = 0
            $clientePagosDelete = $em->getRepository('AppBundle:ClientePago')
                    ->findBy(array('recibo' => $recibo, 'activo' => true));

            foreach ($clientePagosDelete as $clientePago) {
                $clientePago->setActivo(0);
                $clientePago->setUpdatedBy($this->getUser()->getId());
                $clientePago->setUpdatedAt(new \DateTime("now"));
            }   
            //**********************************************************************

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setUpdatedBy($this->getUser()->getId());
                $clientePago->setUpdatedAt(new \DateTime("now"));

                if (is_null($clientePago->getId())){     
                    $clientePago->setCreatedBy($this->getUser()->getId());
                    $clientePago->setCreatedAt(new \DateTime("now"));
                    $em->persist($clientePago);
                }
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            $disponible = $recibo->getTotal();
            foreach($reciboComprobantes as $reciboComprobante) {
                $comprobante = $reciboComprobante->getComprobante();
                if ($disponible >= $comprobante->getPendiente()) {
                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible -= $comprobante->getPendiente();
                }
                else {
                    $pendiente = $comprobante->getPendiente() - $disponible;
                    $importe = $disponible;
                    $disponible = 0;
                }

                $comprobante->setPendiente($pendiente);
                $comprobante->setUpdatedBy($this->getUser()->getId());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setUpdatedBy($this->getUser()->getId());
                $reciboComprobante->setUpdatedAt(new \DateTime("now"));
            }

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }

        return $this->render('recibo/edit.html.twig', array(
            'recibo' => $recibo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'reciboComprobantes' => $reciboComprobantes,
        ));
    }

    /**
     * Deletes a recibo entity.
     *
     */
    public function deleteAction(Request $request, Recibo $recibo)
    {
        $em = $this->getDoctrine()->getManager();

        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        foreach($clientePagos as $clientePago) {
            $clientePago->setActivo(false);
            $clientePago->setUpdatedBy($this->getUser()->getId());
            $clientePago->setUpdatedAt(new \DateTime("now"));
        }

        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        foreach($reciboComprobantes as $reciboComprobante) {
            $comprobante = $reciboComprobante->getComprobante();

            $pendiente = $comprobante->getPendiente() + $reciboComprobante->getImporte();
            $comprobante->setPendiente($pendiente);

            $reciboComprobante->setActivo(false);
            $reciboComprobante->setUpdatedBy($this->getUser()->getId());
            $reciboComprobante->setUpdatedAt(new \DateTime("now"));
        }

        $recibo->setActivo(false);
        $recibo->setUpdatedBy($this->getUser()->getId());
        $recibo->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('recibo_index');
    }

    /**
     * Creates a form to delete a recibo entity.
     *
     * @param Recibo $recibo The recibo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recibo $recibo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recibo_delete', array('id' => $recibo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
