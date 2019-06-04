<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recibo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ReciboComprobante;
use AppBundle\Entity\Cliente;
use Symfony\Component\HttpFoundation\Response;


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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $recibos = $em->getRepository('AppBundle:Recibo')->findBy(Array('activo' => 1, 'sucursal' => $this->getUser()->getSucursal()));

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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

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
            $recibo->setCreatedBy($this->getUser());
            $recibo->setCreatedAt(new \DateTime("now"));
            $recibo->setUpdatedBy($this->getUser());
            $recibo->setUpdatedAt(new \DateTime("now"));

            $em->persist($recibo);

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setCreatedBy($this->getUser());
                $clientePago->setCreatedAt(new \DateTime("now"));
                $clientePago->setUpdatedBy($this->getUser());
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
                $comprobante->setUpdatedBy($this->getUser());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante->setRecibo($recibo);
                $reciboComprobante->setComprobante($comprobante);
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setActivo(1);
                $reciboComprobante->setCreatedBy($this->getUser());
                $reciboComprobante->setCreatedAt(new \DateTime("now"));
                $reciboComprobante->setUpdatedBy($this->getUser());
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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

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
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

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
            $recibo->setCreatedBy($this->getUser());
            $recibo->setCreatedAt(new \DateTime("now"));
            $recibo->setUpdatedBy($this->getUser());
            $recibo->setUpdatedAt(new \DateTime("now"));

            $em->persist($recibo);

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setCreatedBy($this->getUser());
                $clientePago->setCreatedAt(new \DateTime("now"));
                $clientePago->setUpdatedBy($this->getUser());
                $clientePago->setUpdatedAt(new \DateTime("now"));

                $em->persist($clientePago);
            }

            //Recorro los comprobantes y sumo todos los pendientes de las notas de credito
            $disponible = $recibo->getTotal();
            foreach($comprobantes_id_array as $comprobante_id) {
                $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                if ($comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO C' ) {

                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible += $comprobante->getPendiente();
                }
                else {
                    //En este 1er bucle solo utilizo las notas de credito
                    continue;
                }

                $comprobante->setPendiente($pendiente);
                $comprobante->setUpdatedBy($this->getUser());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante = new ReciboComprobante();
                $reciboComprobante->setRecibo($recibo);
                $reciboComprobante->setComprobante($comprobante);
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setActivo(1);
                $reciboComprobante->setCreatedBy($this->getUser());
                $reciboComprobante->setCreatedAt(new \DateTime("now"));
                $reciboComprobante->setUpdatedBy($this->getUser());
                $reciboComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($reciboComprobante);
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            foreach($comprobantes_id_array as $comprobante_id) {
                $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                if ($comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTAS DE CREDITO C' ) {

                    //En este 2do bucle utilizo las facturas, notas de debito u otro comprobante
                    //cuyo importe incremente el total a pagar
                    continue;
                }
                else {
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
                }

                $comprobante->setPendiente($pendiente);
                $comprobante->setUpdatedBy($this->getUser());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante = new ReciboComprobante();
                $reciboComprobante->setRecibo($recibo);
                $reciboComprobante->setComprobante($comprobante);
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setActivo(1);
                $reciboComprobante->setCreatedBy($this->getUser());
                $reciboComprobante->setCreatedAt(new \DateTime("now"));
                $reciboComprobante->setUpdatedBy($this->getUser());
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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

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
            $recibo->setUpdatedBy($this->getUser());
            $recibo->setUpdatedAt(new \DateTime("now"));

            //**********************************************************************
            //ESTA parte es para que funcione el delete de pagos.
            //Basicamente seteo a todos los articulos ya existen en la base de datos con 
            //Activo = 0
            $clientePagosDelete = $em->getRepository('AppBundle:ClientePago')
                    ->findBy(array('recibo' => $recibo, 'activo' => true));

            foreach ($clientePagosDelete as $clientePago) {
                $clientePago->setActivo(0);
                $clientePago->setUpdatedBy($this->getUser());
                $clientePago->setUpdatedAt(new \DateTime("now"));
            }   
            //**********************************************************************

            $clientePagos = $recibo->getclientePagos()->toArray();

            foreach($clientePagos as $clientePago) {
                $clientePago->setRecibo($recibo);
                $clientePago->setActivo(1);
                $clientePago->setUpdatedBy($this->getUser());
                $clientePago->setUpdatedAt(new \DateTime("now"));

                if (is_null($clientePago->getId())){     
                    $clientePago->setCreatedBy($this->getUser());
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
                $comprobante->setUpdatedBy($this->getUser());
                $comprobante->setUpdatedAt(new \DateTime("now"));

                //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
                $reciboComprobante->setImporte($importe);
                $reciboComprobante->setUpdatedBy($this->getUser());
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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        foreach($clientePagos as $clientePago) {
            $clientePago->setActivo(false);
            $clientePago->setUpdatedBy($this->getUser());
            $clientePago->setUpdatedAt(new \DateTime("now"));
        }

        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        foreach($reciboComprobantes as $reciboComprobante) {
            $comprobante = $reciboComprobante->getComprobante();

            $pendiente = $comprobante->getPendiente() + $reciboComprobante->getImporte();
            $comprobante->setPendiente($pendiente);

            $reciboComprobante->setActivo(false);
            $reciboComprobante->setUpdatedBy($this->getUser());
            $reciboComprobante->setUpdatedAt(new \DateTime("now"));
        }

        $recibo->setActivo(false);
        $recibo->setUpdatedBy($this->getUser());
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
