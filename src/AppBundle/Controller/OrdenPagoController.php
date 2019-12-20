<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenPago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\OrdenPagoComprobante;
use AppBundle\Entity\Proveedor;
use AppBundle\Entity\LibroCajaDetalle;

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
        
        return $this->render('ordenpago/index.html.twig', array(
            'ordenPagos' => $ordenPagos,
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
            //Por el momento no permito dejar plata a cuenta
            if ($ordenPago->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('ordenpago_new', array('request' => $request, 'comprobante' => $comprobante->getId()));
            }

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

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($proveedorPago->getPagoTipo());
                $libroCajaDetalle->setProveedorPago($proveedorPago);
                $libroCajaDetalle->setOrigen('Orden de Pago');
                $libroCajaDetalle->setTipo('Egreso de Caja');
                $libroCajaDetalle->setDescripcion($ordenPago->getNumero());
                $libroCajaDetalle->setImporte($proveedorPago->getImporte());
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

            //Por el momento no permito dejar plata a cuenta
            if ($ordenPago->getDisponible() > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El total de los pagos no puede superar el total pendiente.');

                return $this->redirectToRoute('ordenpago_proveedor_new', array('request' => $request, 'proveedor' => $proveedor->getId()));
            }

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

                $libroCajaDetalle = new Librocajadetalle();
                $libroCajaDetalle->setLibroCaja($libroCaja);
                $libroCajaDetalle->setPagoTipo($proveedorPago->getPagoTipo());
                $libroCajaDetalle->setProveedorPago($proveedorPago);
                $libroCajaDetalle->setOrigen('Orden de Pago');
                $libroCajaDetalle->setTipo('Egreso de Caja');
                $libroCajaDetalle->setDescripcion($ordenPago->getNumero());
                $libroCajaDetalle->setImporte($proveedorPago->getImporte());
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
}