<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recibo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ReciboComprobante;
use AppBundle\Entity\Cliente;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\LibroCajaDetalle;
use Symfony\Component\HttpFoundation\JsonResponse;


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
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $recibos = $em->getRepository('AppBundle:Recibo')->findByTexto($this->getUser()->getSucursal()->getId(), $texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $recibos,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('recibo/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto,
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
        $recibo->setCliente($comprobante->getCliente());

        $form = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $reciboComprobante = new ReciboComprobante();
        $reciboComprobante->setComprobante($comprobante);
        $reciboComprobante->setImporte($comprobante->getPendiente());
        
        $reciboComprobantes[] = $reciboComprobante;

        if ($form->isSubmitted() && $form->isValid()) {
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

            $em = $this->getDoctrine()->getManager();

            $libroCaja = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $recibo->getFecha(), 'sucursal' => $this->getUser()->getSucursal(), 'activo' => 1));

            if (is_null($libroCaja)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja con la fecha que ingresó. Debe generar uno antes de cargar recibos.');

                return $this->redirectToRoute('recibo_new', array('request' => $request, 'comprobante' => $comprobante->getId()));
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

            $recibo->setCliente($comprobante->getCliente());
            $recibo->setSucursal($sucursal);
            $recibo->setNumero($max_numero_recibo+1);
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

                $categoria_ingreso_recibo = $em->getRepository('AppBundle:MovimientoCategoria')->find(1);

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($clientePago->getPagoTipo());
                $libroCajaDetalle->setClientePago($clientePago);
                $libroCajaDetalle->setOrigen('Recibo');
                $libroCajaDetalle->setTipo('Ingreso a Caja');
                $libroCajaDetalle->setDescripcion($recibo->getNumero());
                $libroCajaDetalle->setImporte($clientePago->getImporte());
                $libroCajaDetalle->setMovimientoCategoria($categoria_ingreso_recibo);
                $libroCajaDetalle->setActivo(true);
                $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
                $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
                $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
                $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

                if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                    $saldo = $libroCaja->getSaldoFinal();
                    $saldo += $libroCajaDetalle->getImporte();
                    $libroCaja->setSaldoFinal($saldo);
                }

                $em->persist($libroCajaDetalle);
            }

            //Recorro los comprobantes y sumo todos los pendientes de las NOTA de credito
            $disponible = $recibo->getTotal();
            foreach($comprobantes_id_array as $comprobante_id) {
                $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible += $comprobante->getPendiente();
                }
                else {
                    //En este 1er bucle solo utilizo las NOTA de credito
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

                if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                    //En este 2do bucle utilizo las facturas, NOTA de debito u otro comprobante
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

            //Actualizo el saldo del cliente
            $cliente = $recibo->getCliente();
            $cliente_saldo_actualizado = $cliente->getSaldo() + $recibo->getTotal();
            $cliente->setSaldo($cliente_saldo_actualizado);
            $recibo->setSaldo($cliente_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }
        
        return $this->render('recibo/new.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
            'reciboComprobantes' => $reciboComprobantes,
            'cliente' => $recibo->getCliente(),
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
        $recibo->setCliente($cliente);
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
              
        $reciboComprobantes = array();
        
        foreach($comprobantes as $comprobante) {
            $reciboComprobante = new ReciboComprobante();
            $reciboComprobante->setComprobante($comprobante);
            $reciboComprobante->setImporte($comprobante->getPendiente());
            
            $reciboComprobantes[] = $reciboComprobante;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

            $libroCaja = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $recibo->getFecha(), 'sucursal' => $this->getUser()->getSucursal(), 'activo' => 1));

            if (is_null($libroCaja)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja con la fecha que ingresó. Debe generar uno antes de cargar recibos.');

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

            $recibo->setCliente($cliente);
            $recibo->setSucursal($sucursal);
            $recibo->setNumero($max_numero_recibo+1);
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

                $categoria_ingreso_recibo = $em->getRepository('AppBundle:MovimientoCategoria')->find(1);

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($clientePago->getPagoTipo());
                $libroCajaDetalle->setClientePago($clientePago);
                $libroCajaDetalle->setOrigen('Recibo');
                $libroCajaDetalle->setTipo('Ingreso a Caja');
                $libroCajaDetalle->setDescripcion($recibo->getNumero());
                $libroCajaDetalle->setImporte($clientePago->getImporte());
                $libroCajaDetalle->setMovimientoCategoria($categoria_ingreso_recibo);
                $libroCajaDetalle->setActivo(true);
                $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
                $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
                $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
                $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

                if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                    $saldo = $libroCaja->getSaldoFinal();
                    $saldo += $libroCajaDetalle->getImporte();
                    $libroCaja->setSaldoFinal($saldo);
                }

                $em->persist($libroCajaDetalle);
            }

            //Recorro los comprobantes y sumo todos los pendientes de las NOTA de credito
            $disponible = $recibo->getTotal();
            foreach($comprobantes_id_array as $comprobante_id) {
                $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                    $pendiente = 0;
                    $importe = $comprobante->getPendiente();
                    $disponible += $comprobante->getPendiente();
                }
                else {
                    //En este 1er bucle solo utilizo las NOTA de credito
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

                if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                    $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                    //En este 2do bucle utilizo las facturas, NOTA de debito u otro comprobante
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

            //Actualizo el saldo del cliente
            $cliente = $recibo->getCliente();
            $cliente_saldo_actualizado = $cliente->getSaldo() + $recibo->getTotal();
            $cliente->setSaldo($cliente_saldo_actualizado);
            $recibo->setSaldo($cliente_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }
        
        return $this->render('recibo/new.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
            'reciboComprobantes' => $reciboComprobantes,
            'cliente' => $recibo->getCliente(),
        ));
    }

    public function clienteEditAction(Request $request, Recibo $recibo)
    {
        $em = $this->getDoctrine()->getManager();
        $cliente = $recibo->getCliente();
        $cliente_backup = $recibo->getCliente();
        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo' => $recibo, 'activo' => 1));

        foreach($clientePagos as $clientePago) {
            $recibo->getClientePagos()->add($clientePago);
        }

        $form = $this->createForm('AppBundle\Form\ReciboType', $recibo);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo' => $recibo, 'activo' => 1));

        $comprobantes = $em->createQuery('SELECT c
            FROM AppBundle:Comprobante c
            WHERE c.cliente = :cliente
            AND c.activo = 1
            AND c.movimiento = \'Venta\'
            AND c.pendiente > 0')
              ->setParameter('cliente', $cliente)
              ->getResult();
              
        //$reciboComprobantes = array();
        
        foreach($comprobantes as $comprobante) {
            $reciboComprobante = new ReciboComprobante();
            $reciboComprobante->setComprobante($comprobante);
            $reciboComprobante->setImporte($comprobante->getPendiente());
            
            $reciboComprobantes[] = $reciboComprobante;
        }

        if ($form->isSubmitted()) {
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($recibo->getObservaciones())) {
                $recibo->setObservaciones('');
            }

            $recibo->setCliente($cliente_backup);
            $recibo->setUpdatedBy($this->getUser());
            $recibo->setUpdatedAt(new \DateTime("now"));

            //Recorro los comprobantes y sumo todos los pendientes de las NOTA de credito
            $disponible = $recibo->getTotal();
            foreach($comprobantes_id_array as $comprobante_id) {
                if ($comprobante_id['recibo_comprobante_id'] == 0) {
                    //Se trata de un comprobante que estoy agregando
                    $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                    if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                        $pendiente = 0;
                        $importe = $comprobante->getPendiente();
                        $disponible += $comprobante->getPendiente();
                    }
                    else {
                        //En este 1er bucle solo utilizo las NOTA de credito
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
                else {
                    //Se trata de un comprobante que ya había agregado anteriormente
                    $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                    if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                        $disponible += $comprobante->getPendiente();
                    }
                    else {
                        //En este 1er bucle solo utilizo las NOTA de credito
                        continue;
                    }
                }
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            foreach($comprobantes_id_array as $comprobante_id) {
                if ($comprobante_id['recibo_comprobante_id'] == 0) {
                    $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                    if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                        //En este 2do bucle utilizo las facturas, NOTA de debito u otro comprobante
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
                else {
                    $comprobante = $em->getRepository('AppBundle:Comprobante')->find($comprobante_id['id']);

                    if ($comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO A' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO B' ||
                        $comprobante->getTipo()->getDescripcion() == 'NOTA DE CREDITO C' ) {

                        //En este 2do bucle utilizo las facturas, NOTA de debito u otro comprobante
                        //cuyo importe incremente el total a pagar
                        continue;
                    }
                    else {
                        if ($disponible >= $comprobante->getPendiente()) {
                            $disponible -= $comprobante->getPendiente();
                        }
                        else {
                            $disponible = 0;
                        }
                    }
                }
            }

            $em->flush();

            return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
        }
        
        return $this->render('recibo/edit.html.twig', array(
            'recibo' => $recibo,
            'form' => $form->createView(),
            'reciboComprobantes' => $reciboComprobantes,
            'cliente' => $recibo->getCliente(),
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

            $libroCajaDetalle = $em->getRepository('AppBundle:LibroCajaDetalle')->findOneBy(Array('clientePago' => $clientePago, 'activo' => 1));

            $libroCaja = $libroCajaDetalle->getLibroCaja();
            
            $libroCajaDetalle->setActivo(false);
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

            if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $saldo = $libroCaja->getSaldoFinal();
                $saldo -= $libroCajaDetalle->getImporte();
                $libroCaja->setSaldoFinal($saldo);
            }
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

        //Actualizo el saldo del cliente
        $cliente = $recibo->getCliente();
        $cliente_saldo_actualizado = $cliente->getSaldo() - $recibo->getTotal();
        $cliente->setSaldo($cliente_saldo_actualizado);
        $recibo->setSaldo($cliente_saldo_actualizado);

        $em->flush();

        return $this->redirectToRoute('recibo_index');
    }

    public function clienteDeleteAction(Request $request, ReciboComprobante $reciboComprobante)
    {
        $em = $this->getDoctrine()->getManager();

        $comprobante = $reciboComprobante->getComprobante();
        $recibo = $reciboComprobante->getRecibo();

        $pendiente = $comprobante->getPendiente() + $reciboComprobante->getImporte();
        $comprobante->setPendiente($pendiente);

        $disponible = $recibo->getDisponible() + $reciboComprobante->getImporte();
        $recibo->setDisponible($disponible);
        $recibo->setUpdatedBy($this->getUser());
        $recibo->setUpdatedAt(new \DateTime("now"));

        $reciboComprobante->setActivo(false);
        $reciboComprobante->setUpdatedBy($this->getUser());
        $reciboComprobante->setUpdatedAt(new \DateTime("now"));
        
        $em->flush();

        return $this->redirectToRoute('recibo_show', array('id' => $recibo->getId()));
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

    /**
     * Imprime la factura electrónica generada por medio de WS con afip.
     *
     */
    public function imprimirAction(Request $request, Recibo $recibo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Recibo', 'Imprimir', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $clientePagos = $em->getRepository('AppBundle:ClientePago')->findBy(Array('recibo'=>$recibo,  'activo'=>1));
        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('recibo'=>$recibo, 'activo' => 1));

        $html = $this->renderView('recibo/imprimir.html.twig', array(
            'recibo' => $recibo,
            'clientePagos' => $clientePagos,
            'reciboComprobantes' => $reciboComprobantes,
            'reciboTotalTexto' => $this->numtoletras($recibo->getTotal()),
            'empresa' => $this->container->getParameter('empresa'),
            'empresaRazonSocial' => $this->container->getParameter('empresa_razon_social'),
            'empresaCondicion' => $this->container->getParameter('empresa_condicion'),
            'empresaCuit' => $this->container->getParameter('empresa_cuit'),
            'empresaIngresosBrutos' => $this->container->getParameter('empresa_ingresos_brutos'),
            'empresaInicioActividades' => $this->container->getParameter('empresa_inicio_actividades'),
        )
        );

        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        //$pdf->SetAuthor('Our Code World');
        //$pdf->SetTitle(('Our Code World Title'));
        //$pdf->SetSubject('Our Code World Subject');
        //$pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        
        $filename = 'Recibo '.$recibo->getNumero();
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }

    /*
    Las siguientes dos funciones deberrían ir en algún contralor que tenga funciones de todo tipo
    que sean utilizadas de todos lados
    */
    public function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
    //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO CON $xdecimales/100";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN CON $xdecimales/100";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " CON $xdecimales/100"; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

    private function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }

    public function findSelect2Action(Request $request) {
        $em = $em = $this->getDoctrine()->getManager('default');
        
        $text_search = $request->get('q');
        $pageLimit = $request->get('page_limit');

        if (!is_numeric($pageLimit) || $pageLimit > 10) {
            $pageLimit = 10;
        }

        $result = $em->createQuery('
                        SELECT r.id as id, r.id as text
                        FROM AppBundle:Recibo r
                        WHERE r.activo = 1 AND r.id LIKE :text_search
                        ORDER BY r.id ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }
}
