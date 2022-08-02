<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenPago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\OrdenPagoComprobante;
use AppBundle\Entity\Proveedor;
use AppBundle\Entity\LibroCajaDetalle;
use AppBundle\Entity\Cheque;

/**
 * Ordenpago controller.
 *
 */
class OrdenPagoController extends Controller
{
    /**
     * Lists all ordenPago entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $ordenPagos = $em->getRepository('AppBundle:OrdenPago')->findByTexto($this->getUser()->getSucursal()->getId(), $texto);
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $ordenPagos,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('ordenpago/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto,
        ));
    }

    /**
     * Creates a new ordenPago entity.
     *
     */
    public function newAction(Request $request, Comprobante $comprobante)
    {
        $ordenPago = new OrdenPago();
        $ordenPago->setFecha(new \DateTime("now"));
        $ordenPago->setProveedor($comprobante->getProveedor());

        $form = $this->createForm('AppBundle\Form\OrdenPagoType', $ordenPago);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $ordenPagoComprobante = new OrdenPagoComprobante();
        $ordenPagoComprobante->setComprobante($comprobante);
        $ordenPagoComprobante->setImporte($comprobante->getPendiente());
        
        $ordenPagoComprobantes[] = $ordenPagoComprobante;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $libroCaja = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $ordenPago->getFecha(), 'sucursal' => $this->getUser()->getSucursal(), 'activo' => 1));

            if (is_null($libroCaja)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja con la fecha que ingresó. Debe generar uno antes de cargar ordenes de pago.');

                return $this->redirectToRoute('ordenpago_new', array('request' => $request, 'comprobante' => $comprobante->getId()));
            }

            $max_numero_ordenpago = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:OrdenPago', 'c')
             ->getQuery()
             ->getSingleScalarResult();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($ordenPago->getObservaciones())) {
                $ordenPago->setObservaciones('');
            }

            $ordenPago->setProveedor($comprobante->getProveedor());
            $ordenPago->setSucursal($sucursal);
            $ordenPago->setNumero($max_numero_ordenpago+1);
            $ordenPago->setActivo(1);
            $ordenPago->setCreatedBy($this->getUser());
            $ordenPago->setCreatedAt(new \DateTime("now"));
            $ordenPago->setUpdatedBy($this->getUser());
            $ordenPago->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenPago);

            $proveedorPagos = $ordenPago->getproveedorPagos()->toArray();

            foreach($proveedorPagos as $proveedorPago) {
                $proveedorPago->setOrdenPago($ordenPago);
                $proveedorPago->setActivo(1);
                $proveedorPago->setCreatedBy($this->getUser());
                $proveedorPago->setCreatedAt(new \DateTime("now"));
                $proveedorPago->setUpdatedBy($this->getUser());
                $proveedorPago->setUpdatedAt(new \DateTime("now"));

                $em->persist($proveedorPago);

                //Si el pago es un cheque, lo creo en la tabla de cheques
                if ($proveedorPago->getPagoTipo()->getNombre() == 'Cheque') {
                    $cheque = new Cheque();
                    $cheque->setBanco($proveedorPago->getBanco());
                    $cheque->setFecha($proveedorPago->getFecha());
                    $cheque->setNumero($proveedorPago->getNumero());
                    $cheque->setImporte($proveedorPago->getImporte());
                    $cheque->setOrdenPago($ordenPago);
                    $cheque->setEstado('Emitido');
                    $cheque->setActivo(1);
                    $cheque->setCreatedBy($this->getUser());
                    $cheque->setCreatedAt(new \DateTime("now"));
                    $cheque->setUpdatedBy($this->getUser());
                    $cheque->setUpdatedAt(new \DateTime("now"));

                    $em->persist($cheque);

                    $proveedorPago->setCheque($cheque);
                }

                $categoria_egreso_orden_pago = $em->getRepository('AppBundle:MovimientoCategoria')->find(2);

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($proveedorPago->getPagoTipo());
                $libroCajaDetalle->setProveedorPago($proveedorPago);
                $libroCajaDetalle->setOrigen('Orden de Pago');
                $libroCajaDetalle->setTipo('Egreso de Caja');
                $libroCajaDetalle->setDescripcion($ordenPago->getNumero());
                $libroCajaDetalle->setImporte($proveedorPago->getImporte());
                $libroCajaDetalle->setMovimientoCategoria($categoria_egreso_orden_pago);
                $libroCajaDetalle->setActivo(true);
                $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
                $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
                $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
                $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

                if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                    $saldo = $libroCaja->getSaldoFinal();
                    $saldo -= $libroCajaDetalle->getImporte();
                    $libroCaja->setSaldoFinal($saldo);
                }

                $em->persist($libroCajaDetalle);
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            $disponible = $ordenPago->getTotal();
            foreach($ordenPagoComprobantes as $ordenPagoComprobante) {
                $comprobante = $ordenPagoComprobante->getComprobante();
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
                $ordenPagoComprobante->setOrdenPago($ordenPago);
                $ordenPagoComprobante->setComprobante($comprobante);
                $ordenPagoComprobante->setImporte($importe);
                $ordenPagoComprobante->setActivo(1);
                $ordenPagoComprobante->setCreatedBy($this->getUser());
                $ordenPagoComprobante->setCreatedAt(new \DateTime("now"));
                $ordenPagoComprobante->setUpdatedBy($this->getUser());
                $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordenPagoComprobante);
            }

            //Actualizo el saldo del proveedor
            $proveedor = $ordenPago->getProveedor();
            $proveedor_saldo_actualizado = $proveedor->getSaldo() + $ordenPago->getTotal();
            $proveedor->setSaldo($proveedor_saldo_actualizado);
            $ordenPago->setSaldo($proveedor_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('ordenpago_show', array('id' => $ordenPago->getId()));
        }
        
        return $this->render('ordenpago/new.html.twig', array(
            'ordenPago' => $ordenPago,
            'form' => $form->createView(),
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
        ));
    }

/**
     * Creates a new ordenPago entity.
     *
     */
    public function proveedorNewAction(Request $request, Proveedor $proveedor)
    {
        $em = $this->getDoctrine()->getManager();
        $ordenPago = new OrdenPago();
        $ordenPago->setFecha(new \DateTime("now"));
        $ordenPago->setProveedor($proveedor);
        $form = $this->createForm('AppBundle\Form\OrdenPagoType', $ordenPago);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $comprobantes = $em->createQuery('SELECT c
            FROM AppBundle:Comprobante c
            WHERE c.proveedor = :proveedor
            AND c.activo = 1
            AND c.movimiento = \'Compra\'
            AND c.pendiente > 0')
              ->setParameter('proveedor', $proveedor)
              ->getResult();
        
        $ordenPagoComprobantes = array();
          
        foreach($comprobantes as $comprobante) {
            $ordenPagoComprobante = new OrdenPagoComprobante();
            $ordenPagoComprobante->setComprobante($comprobante);
            $ordenPagoComprobante->setImporte($comprobante->getPendiente());
            
            $ordenPagoComprobantes[] = $ordenPagoComprobante;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

            $libroCaja = $em->getRepository('AppBundle:LibroCaja')->findOneBy(Array('fecha' => $ordenPago->getFecha(), 'sucursal' => $this->getUser()->getSucursal(), 'activo' => 1));

            if (is_null($libroCaja)) {
                $this->get('session')->getFlashbag()->add('warning', 'No existe ningún libro caja con la fecha que ingresó. Debe generar uno antes de cargar ordenes de pago.');

                return $this->redirectToRoute('ordenpago_proveedor_new', array('request' => $request, 'proveedor' => $proveedor->getId()));
            }

            $max_numero_ordenpago = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:OrdenPago', 'c')
             ->getQuery()
             ->getSingleScalarResult();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($ordenPago->getObservaciones())) {
                $ordenPago->setObservaciones('');
            }

            $ordenPago->setProveedor($proveedor);
            $ordenPago->setSucursal($sucursal);
            $ordenPago->setNumero($max_numero_ordenpago+1);
            $ordenPago->setActivo(1);
            $ordenPago->setCreatedBy($this->getUser());
            $ordenPago->setCreatedAt(new \DateTime("now"));
            $ordenPago->setUpdatedBy($this->getUser());
            $ordenPago->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenPago);

            $proveedorPagos = $ordenPago->getproveedorPagos()->toArray();

            foreach($proveedorPagos as $proveedorPago) {
                $proveedorPago->setOrdenPago($ordenPago);
                $proveedorPago->setActivo(1);
                $proveedorPago->setCreatedBy($this->getUser());
                $proveedorPago->setCreatedAt(new \DateTime("now"));
                $proveedorPago->setUpdatedBy($this->getUser());
                $proveedorPago->setUpdatedAt(new \DateTime("now"));

                $em->persist($proveedorPago);

                //Si el pago es un cheque, lo creo en la tabla de cheques
                if ($proveedorPago->getPagoTipo()->getNombre() == 'Cheque') {
                    $cheque = new Cheque();
                    $cheque->setBanco($proveedorPago->getBanco());
                    $cheque->setFecha($proveedorPago->getFecha());
                    $cheque->setNumero($proveedorPago->getNumero());
                    $cheque->setImporte($proveedorPago->getImporte());
                    $cheque->setOrdenPago($ordenPago);
                    $cheque->setEstado('Emitido');
                    $cheque->setActivo(1);
                    $cheque->setCreatedBy($this->getUser());
                    $cheque->setCreatedAt(new \DateTime("now"));
                    $cheque->setUpdatedBy($this->getUser());
                    $cheque->setUpdatedAt(new \DateTime("now"));

                    $em->persist($cheque);

                    $proveedorPago->setCheque($cheque);
                }

                $categoria_egreso_orden_pago = $em->getRepository('AppBundle:MovimientoCategoria')->find(2);

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($proveedorPago->getPagoTipo());
                $libroCajaDetalle->setProveedorPago($proveedorPago);
                $libroCajaDetalle->setOrigen('Orden de Pago');
                $libroCajaDetalle->setTipo('Egreso de Caja');
                $libroCajaDetalle->setDescripcion($ordenPago->getNumero());
                $libroCajaDetalle->setImporte($proveedorPago->getImporte());
                $libroCajaDetalle->setMovimientoCategoria($categoria_egreso_orden_pago);
                $libroCajaDetalle->setActivo(true);
                $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
                $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
                $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
                $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

                if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                    $saldo = $libroCaja->getSaldoFinal();
                    $saldo -= $libroCajaDetalle->getImporte();
                    $libroCaja->setSaldoFinal($saldo);
                }

                $em->persist($libroCajaDetalle);
            }

            //Recorro los comprobantes y sumo todos los pendientes de las NOTA de credito
            $disponible = $ordenPago->getTotal();
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
                $ordenPagoComprobante = new OrdenPagoComprobante();
                $ordenPagoComprobante->setOrdenPago($ordenPago);
                $ordenPagoComprobante->setComprobante($comprobante);
                $ordenPagoComprobante->setImporte($importe);
                $ordenPagoComprobante->setActivo(1);
                $ordenPagoComprobante->setCreatedBy($this->getUser());
                $ordenPagoComprobante->setCreatedAt(new \DateTime("now"));
                $ordenPagoComprobante->setUpdatedBy($this->getUser());
                $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordenPagoComprobante);
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
                $ordenPagoComprobante = new OrdenPagoComprobante();
                $ordenPagoComprobante->setOrdenPago($ordenPago);
                $ordenPagoComprobante->setComprobante($comprobante);
                $ordenPagoComprobante->setImporte($importe);
                $ordenPagoComprobante->setActivo(1);
                $ordenPagoComprobante->setCreatedBy($this->getUser());
                $ordenPagoComprobante->setCreatedAt(new \DateTime("now"));
                $ordenPagoComprobante->setUpdatedBy($this->getUser());
                $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordenPagoComprobante);
            }

            //Actualizo el saldo del proveedor
            $proveedor = $ordenPago->getProveedor();
            $proveedor_saldo_actualizado = $proveedor->getSaldo() + $ordenPago->getTotal();
            $proveedor->setSaldo($proveedor_saldo_actualizado);
            $ordenPago->setSaldo($proveedor_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('ordenpago_show', array('id' => $ordenPago->getId()));
        }
        
        return $this->render('ordenpago/new.html.twig', array(
            'ordenPago' => $ordenPago,
            'form' => $form->createView(),
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
        ));
    }

    public function proveedorEditAction(Request $request, OrdenPago $ordenPago)
    {
        $em = $this->getDoctrine()->getManager();

        $proveedor = $ordenPago->getProveedor();
        $proveedor_backup = $ordenPago->getProveedor();
        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findBy(Array('ordenPago' => $ordenPago, 'activo' => 1));

        foreach($proveedorPagos as $proveedorPago) {
            $ordenPago->getProveedorPagos()->add($proveedorPago);
        }

        $form = $this->createForm('AppBundle\Form\OrdenPagoType', $ordenPago);
        $form->handleRequest($request);

        //Esto por ahora lo dejo así pero habría que ver si hay que hacerlo "bien"
        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('ordenPago' => $ordenPago, 'activo' => 1));

        $comprobantes = $em->createQuery('SELECT c
            FROM AppBundle:Comprobante c
            WHERE c.proveedor = :proveedor
            AND c.activo = 1
            AND c.movimiento = \'Compra\'
            AND c.pendiente > 0')
              ->setParameter('proveedor', $proveedor)
              ->getResult();
        
        //$ordenPagoComprobantes = array();
          
        foreach($comprobantes as $comprobante) {
            $ordenPagoComprobante = new OrdenPagoComprobante();
            $ordenPagoComprobante->setComprobante($comprobante);
            $ordenPagoComprobante->setImporte($comprobante->getPendiente());
            
            $ordenPagoComprobantes[] = $ordenPagoComprobante;
        }

        if ($form->isSubmitted()) {
            $comprobantes_id_array = stripcslashes($request->get('comprobantes'));
            $comprobantes_id_array = json_decode($comprobantes_id_array,TRUE);

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            if (is_null($ordenPago->getObservaciones())) {
                $ordenPago->setObservaciones('');
            }

            $ordenPago->setProveedor($proveedor_backup);
            $ordenPago->setUpdatedBy($this->getUser());
            $ordenPago->setUpdatedAt(new \DateTime("now"));

            //Recorro los comprobantes y sumo todos los pendientes de las NOTA de credito
            $disponible = $ordenPago->getTotal();
            foreach($comprobantes_id_array as $comprobante_id) {
                if ($comprobante_id['ordenpago_comprobante_id'] == 0) {
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
                    $ordenPagoComprobante = new OrdenPagoComprobante();
                    $ordenPagoComprobante->setOrdenPago($ordenPago);
                    $ordenPagoComprobante->setComprobante($comprobante);
                    $ordenPagoComprobante->setImporte($importe);
                    $ordenPagoComprobante->setActivo(1);
                    $ordenPagoComprobante->setCreatedBy($this->getUser());
                    $ordenPagoComprobante->setCreatedAt(new \DateTime("now"));
                    $ordenPagoComprobante->setUpdatedBy($this->getUser());
                    $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));

                    $em->persist($ordenPagoComprobante);
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
                if ($comprobante_id['ordenpago_comprobante_id'] == 0) {
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
                    $ordenPagoComprobante = new OrdenPagoComprobante();
                    $ordenPagoComprobante->setOrdenPago($ordenPago);
                    $ordenPagoComprobante->setComprobante($comprobante);
                    $ordenPagoComprobante->setImporte($importe);
                    $ordenPagoComprobante->setActivo(1);
                    $ordenPagoComprobante->setCreatedBy($this->getUser());
                    $ordenPagoComprobante->setCreatedAt(new \DateTime("now"));
                    $ordenPagoComprobante->setUpdatedBy($this->getUser());
                    $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));

                    $em->persist($ordenPagoComprobante);
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

            return $this->redirectToRoute('ordenpago_show', array('id' => $ordenPago->getId()));
        }
        
        return $this->render('ordenpago/edit.html.twig', array(
            'ordenPago' => $ordenPago,
            'form' => $form->createView(),
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
        ));
    }

    /**
     * Finds and displays a ordenPago entity.
     *
     */
    public function showAction(OrdenPago $ordenPago)
    {
        $em = $this->getDoctrine()->getManager();
        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findBy(Array('ordenPago'=>$ordenPago,  'activo'=>1));
        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($ordenPago);

        return $this->render('ordenpago/show.html.twig', array(
            'ordenPago' => $ordenPago,
            'proveedorPagos' => $proveedorPagos,
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenPago entity.
     *
     */
    public function editAction(Request $request, OrdenPago $ordenPago)
    {
        $em = $this->getDoctrine()->getManager();

        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        foreach($proveedorPagos as $proveedorPago) {
            $ordenPago->getProveedorPagos()->add($proveedorPago);
        }

        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($ordenPago);
        $editForm = $this->createForm('AppBundle\Form\OrdenPagoType', $ordenPago);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //Por el momento no permito dejar plata a cuenta
            if ($ordenPago->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('ordenpago_edit', array('request' => $request, 'id' => $ordenPago->getId()));
            }

            $em = $this->getDoctrine()->getManager();

            if (is_null($ordenPago->getObservaciones())) {
                $ordenPago->setObservaciones('');
            }

            $ordenPago->setSaldo(0);
            $ordenPago->setUpdatedBy($this->getUser());
            $ordenPago->setUpdatedAt(new \DateTime("now"));

            //**********************************************************************
            //ESTA parte es para que funcione el delete de pagos.
            //Basicamente seteo a todos los articulos ya existen en la base de datos con 
            //Activo = 0
            $proveedorPagosDelete = $em->getRepository('AppBundle:ProveedorPago')
                    ->findBy(array('ordenPago' => $ordenPago, 'activo' => true));

            foreach ($proveedorPagosDelete as $proveedorPago) {
                $proveedorPago->setActivo(0);
                $proveedorPago->setUpdatedBy($this->getUser());
                $proveedorPago->setUpdatedAt(new \DateTime("now"));
            }   
            //**********************************************************************

            $proveedorPagos = $ordenPago->getproveedorPagos()->toArray();

            foreach($proveedorPagos as $proveedorPago) {
                $proveedorPago->setOrdenPago($ordenPago);
                $proveedorPago->setActivo(1);
                $proveedorPago->setUpdatedBy($this->getUser());
                $proveedorPago->setUpdatedAt(new \DateTime("now"));

                if (is_null($proveedorPago->getId())){     
                    $proveedorPago->setCreatedBy($this->getUser());
                    $proveedorPago->setCreatedAt(new \DateTime("now"));
                    $em->persist($proveedorPago);
                }
            }

            //Recorro los comprobantes y voy pagando mientras haya disponible
            $disponible = $ordenPago->getTotal();
            foreach($ordenPagoComprobantes as $ordenPagoComprobante) {
                $comprobante = $ordenPagoComprobante->getComprobante();
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
                $ordenPagoComprobante->setImporte($importe);
                $ordenPagoComprobante->setUpdatedBy($this->getUser());
                $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));
            }

            $em->flush();

            return $this->redirectToRoute('ordenpago_show', array('id' => $ordenPago->getId()));
        }

        return $this->render('ordenpago/edit.html.twig', array(
            'ordenPago' => $ordenPago,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
        ));
    }

    /**
     * Deletes a ordenPago entity.
     *
     */
    public function deleteAction(Request $request, OrdenPago $ordenPago)
    {
        $em = $this->getDoctrine()->getManager();
        
        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        foreach($proveedorPagos as $proveedorPago) {
            $proveedorPago->setActivo(false);
            $proveedorPago->setUpdatedBy($this->getUser());
            $proveedorPago->setUpdatedAt(new \DateTime("now"));

            //Si el pago es un cheque, lo anulo en la tabla de cheques
            if ($proveedorPago->getPagoTipo()->getNombre() == 'Cheque') {

                $cheque = $proveedorPago->getCheque();
                $cheque->setEstado('Anulado');
                $cheque->setActivo(0);
                $cheque->setUpdatedBy($this->getUser());
                $cheque->setUpdatedAt(new \DateTime("now"));
            }

            $libroCajaDetalle = $em->getRepository('AppBundle:LibroCajaDetalle')->findOneBy(Array('proveedorPago' => $proveedorPago, 'activo' => 1));

            $libroCaja = $libroCajaDetalle->getLibroCaja();
            
            $libroCajaDetalle->setActivo(false);
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

            if ($libroCajaDetalle->getPagoTipo()->getNombre() == ' Efectivo') {
                $saldo = $libroCaja->getSaldoFinal();
                $saldo += $libroCajaDetalle->getImporte();
                $libroCaja->setSaldoFinal($saldo);
            }
        }

        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        foreach($ordenPagoComprobantes as $ordenPagoComprobante) {
            $comprobante = $ordenPagoComprobante->getComprobante();

            $pendiente = $comprobante->getPendiente() + $ordenPagoComprobante->getImporte();
            $comprobante->setPendiente($pendiente);

            $ordenPagoComprobante->setActivo(false);
            $ordenPagoComprobante->setUpdatedBy($this->getUser());
            $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));
        }

        $ordenPago->setActivo(false);
        $ordenPago->setUpdatedBy($this->getUser());
        $ordenPago->setUpdatedAt(new \DateTime("now"));

        //Actualizo el saldo del proveedor
        $proveedor = $ordenPago->getProveedor();
        $proveedor_saldo_actualizado = $proveedor->getSaldo() - $ordenPago->getTotal();
        $proveedor->setSaldo($proveedor_saldo_actualizado);
        $ordenPago->setSaldo($proveedor_saldo_actualizado);

        $em->flush();

        return $this->redirectToRoute('ordenpago_index');
    }

    public function proveedorDeleteAction(Request $request, OrdenPagoComprobante $ordenPagoComprobante)
    {
        $em = $this->getDoctrine()->getManager();

        $comprobante = $ordenPagoComprobante->getComprobante();
        $ordenPago = $ordenPagoComprobante->getOrdenPago();

        $pendiente = $comprobante->getPendiente() + $ordenPagoComprobante->getImporte();
        $comprobante->setPendiente($pendiente);

        $disponible = $ordenPago->getDisponible() + $ordenPagoComprobante->getImporte();
        $ordenPago->setDisponible($disponible);
        $ordenPago->setUpdatedBy($this->getUser());
        $ordenPago->setUpdatedAt(new \DateTime("now"));

        $ordenPagoComprobante->setActivo(false);
        $ordenPagoComprobante->setUpdatedBy($this->getUser());
        $ordenPagoComprobante->setUpdatedAt(new \DateTime("now"));
        
        $em->flush();

        return $this->redirectToRoute('ordenpago_show', array('id' => $ordenPago->getId()));
    }

    /**
     * Creates a form to delete a ordenPago entity.
     *
     * @param OrdenPago $ordenPago The ordenPago entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenPago $ordenPago)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordenpago_delete', array('id' => $ordenPago->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function imprimirAction(Request $request, OrdenPago $ordenPago)
    {
        $em = $this->getDoctrine()->getManager();
        $proveedorPagos = $em->getRepository('AppBundle:ProveedorPago')->findBy(Array('ordenPago'=>$ordenPago,  'activo'=>1));
        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('ordenPago'=>$ordenPago, 'activo' => 1));

        $html = $this->renderView('ordenpago/imprimir.html.twig', array(
            'ordenPago' => $ordenPago,
            'proveedorPagos' => $proveedorPagos,
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
            'ordenPagoTotalTexto' => $this->numtoletras($ordenPago->getTotal()),
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
        
        $filename = 'OrdenPago '.$ordenPago->getNumero();
        
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
}
