<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use AppBundle\Entity\Articulo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Form\ComprobanteType;
use Symfony\Component\Serializer\Serializer;
use AppBundle\Form\ComprobanteDetalleType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Comprobante controller.
 *
 */
class ComprobanteCompraController extends controller
{
    /**
     * Lists all comprobante entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fecha_desde = $request->get('fecha_desde','');
        $fecha_hasta = $request->get('fecha_hasta','');
        $proveedor = $request->get('cliente','');
        $pagado = $request->get('pagado','');

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findByGeneral_compras($this->getUser()->getSucursal()->getId(), $fecha_desde, $fecha_hasta, $proveedor, $pagado);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comprobantes,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('comprobantecompra/index.html.twig', array(
            'pagination' => $pagination,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'proveedor' => $proveedor,
            'pagado' => $pagado,
        ));
    }

    /**
     * Creates a new comprobante entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteCompra', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $comprobante = new Comprobante();
        $comprobante->setFecha(new \DateTime("now"));
        $comprobante->setUsuario($this->getUser());
        $form = $this->createForm(ComprobanteType::class, $comprobante, array('attr' => array('tipo' => 'Compra')));
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            //INICIO Validacion Comprobante Existente
            $comprobantesDuplicado = $em->getRepository('AppBundle:Comprobante')->findBy(Array('proveedor' => $comprobante->getProveedor(), 'puntoVenta' => $comprobante->getPuntoVenta(), 'numero' => $comprobante->getNumero(), 'tipo' => $comprobante->getTipo(), 'activo'=>1, 'movimiento' => 'Compra'));

            if (count($comprobantesDuplicado) > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El Comprobante ya fue cargado');

                return $this->render('comprobantecompra/new.html.twig', array(
                    'comprobante' => $comprobante,
                    'form' => $form->createView(),
                ));
            }
            //FIN Validacion Comprobante Existente

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            $comprobante->setSucursal($sucursal);
            $comprobante->setTotalGanancia(0);
            $comprobante->setMovimiento('Compra');
            $comprobante->setPendiente($comprobante->getTotal());
            $comprobante->setUsuario($this->getUser());
            $comprobante->setActivo(1);

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $comprobante->setCreatedBy($this->getUser());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser());
            $comprobante->setUpdatedAt(new \DateTime("now"));

            $em->persist($comprobante);

            $comprobanteDetalle = new ComprobanteDetalle();
            $comprobanteDetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobanteDetalles as $comprobanteDetalle) {
                //Verifico si hay que agregar el artículo ingresado
                $crear_articulo = false;
                if (is_null($comprobanteDetalle->getArticulo())) {
                    $crear_articulo = true;
                }
                elseif (is_null($comprobanteDetalle->getArticulo()->getId())) {
                    $crear_articulo = true;
                }
                if ($crear_articulo) {
                    $articulo = new Articulo();

                    $categoria = $em->getRepository('AppBundle:ArticuloCategoria')->find(1);
                    $marca = $em->getRepository('AppBundle:ArticuloMarca')->find(1);
                    $iva_21 = $em->getRepository('AppBundle:AfipAlicuota')->findOneBy(array('descripcion' => '21.00'));

                    $articulo->setCodigo('S/A');
                    $articulo->setCategoria($categoria);
                    $articulo->setMarca($marca);
                    $articulo->setDescripcion($comprobanteDetalle->getObservaciones());
                    $articulo->setIva($iva_21); //Asigno 21% de iva, después habría si es correcto
                    $articulo->setForma('');
                    $articulo->setTipoAro('');
                    $articulo->setColorMarco('');
                    $articulo->setColorCristal('');
                    $articulo->setActivo(true);
                    $articulo->setPrecioModifica(1);
                    $articulo->setOrdenTrabajo(1);
                    $articulo->setUltimoComprobante($comprobante);
                    $articulo->setCreatedBy($this->getUser());
                    $articulo->setCreatedAt(new \DateTime("now"));
                    $articulo->setUpdatedBy($this->getUser());
                    $articulo->setUpdatedAt(new \DateTime("now"));

                    $em->persist($articulo);

                    $sucursales = $em->getRepository('AppBundle:Sucursal')->findBy(array('activo' => true));

                    foreach ($sucursales as $sucursal) {
                        $stock = new Stock();

                        $stock->setArticulo($articulo);
                        $stock->setSucursal($sucursal);
                        $stock->setCantidad(0);
                        $stock->setCantidadMinima(1);
                        $stock->setActivo(true);
                        $stock->setCreatedBy($this->getUser());
                        $stock->setCreatedAt(new \DateTime("now"));
                        $stock->setUpdatedBy($this->getUser());
                        $stock->setUpdatedAt(new \DateTime("now"));

                        $em->persist($stock);
                    }

                    $comprobanteDetalle->setArticulo($articulo);
                }
                
                $comprobanteDetalle->setPrecioNeto(0);
                $comprobanteDetalle->setImporteGanancia(0);
                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);
                $comprobanteDetalle->setObservaciones('');
                
                $comprobanteDetalle->setTotalNeto($comprobanteDetalle->getPrecioCosto()*$comprobanteDetalle->getCantidad());
                
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setMovimiento('Compra');
                $comprobanteDetalle->setActivo(1);
                $comprobanteDetalle->setCreatedBy($this->getUser());
                $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                $comprobanteDetalle->setUpdatedBy($this->getUser());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));

                // Actualizacion Stock
                $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

                if (is_null($stock))
                {
                    $stock = new Stock();

                    $stock->setSucursal($sucursal);
                    $stock->setArticulo($comprobanteDetalle->getArticulo());
                    $stock->setCantidadMinima(1);
                    $stock->setActivo(true);
                    $stock->setCreatedBy($this->getUser());
                    $stock->setCreatedAt(new \DateTime("now"));
                    $stock->setUpdatedBy($this->getUser());
                    $stock->setUpdatedAt(new \DateTime("now"));

                    $em->persist($stock);
                }        

                if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                    $cantidad = $stock->getCantidad() + $comprobanteDetalle->getCantidad();
                }
                else {
                    $cantidad = $stock->getCantidad() - $comprobanteDetalle->getCantidad();
                }
                $stock->setCantidad($cantidad);
                $stock->setUpdatedBy($this->getUser());
                $stock->setUpdatedAt(new \DateTime("now"));
                $em->persist($stock);

                $em->persist($comprobanteDetalle);

                //Actualizo datos en el artículo
                $articulo = $comprobanteDetalle->getArticulo();

                $iva = $articulo->getIva()->getDescripcion();
                //calculo el precio con iva sin el porcentaje del 15% de tarjeta
                $articulo_precio_venta_sin_tarjeta = 100 * $comprobanteDetalle->getPrecioVenta() / 115;
                
                //calculo el precio sin iva
                $articulo_precio_venta_sin_iva = 100 * $articulo_precio_venta_sin_tarjeta / (100+$iva);

                $articulo->setPrecioCosto($comprobanteDetalle->getPrecioCosto());
                $articulo->setGananciaPorcentaje($comprobanteDetalle->getPorcentajeGanancia());
                $articulo->setPrecioVentaSinIva($articulo_precio_venta_sin_iva);
                $articulo->setPrecioVenta($comprobanteDetalle->getPrecioVenta());
                $articulo->setUltimoComprobante($comprobante);
            }

            //Actualizo el saldo del proveedor
            $proveedor = $comprobante->getProveedor();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal();
            }
            else {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal();
            }

            $proveedor->setSaldo($proveedor_saldo_actualizado);
            $comprobante->setSaldo($proveedor_saldo_actualizado);

            $em->flush();
            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobantecompra/new.html.twig', array(
            'comprobante' => $comprobante,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comprobante entity.
     *
     */
    public function showAction(Comprobante $comprobante)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteCompra', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobantecompra/show.html.twig', array(
            'comprobante' => $comprobante,
            'comprobantedetalles' => $comprobanteDetalles,
            'ordenPagoComprobantes' => $ordenPagoComprobantes,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comprobante entity.
     *
     */
    public function editAction(Request $request, Comprobante $comprobante)
    {
        $em = $this->getDoctrine()->getManager();
        
        //Guardo el saldo del proveedor antes de editar el comprobante
        $comprobante_saldo_anterior = $comprobante->getTotal();
        
        //Solo puede editarse si la sucursal elegida es la misma del comprobante.
        if ($comprobante->getSucursal()->getId() != $this->getUser()->getSucursal()->getId()) {

            $this->get('session')->getFlashbag()->add('warning', 'Comprobante de sucursal: '.$comprobante->getSucursal().'. La sucursal actual es: '.$this->getUser()->getSucursal().', Cambie de sucursal para editar el registro');

            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        //Si ya tiene ordenes de pago asociadas no se puede modificar
        $recibos = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));
        if (count($recibos) > 0) {
            $this->get('session')->getFlashbag()->add('warning', 'El comprobante ya tiene ordenes de pago asociadas. Edición denegada.');

            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        if (is_null($comprobante->getObservaciones())) {
            $comprobante->setObservaciones('');
        }

        $stockComprobante = array();
        foreach($comprobanteDetalles as $comprobanteDetalle) {
            $comprobante->getComprobanteDetalles()->add($comprobanteDetalle);

            //Guardo el stock de cada artículo devolviendo el stock por si llegase a eliminarse algún item
            $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $cantidad = $stock->getCantidad() - $comprobanteDetalle->getCantidad();
            }
            else {
                $cantidad = $stock->getCantidad() + $comprobanteDetalle->getCantidad();
            }
            $stock->setCantidad($cantidad);
            $stockComprobante[$comprobanteDetalle->getArticulo()->getId()] = $stock;
        }

        $editForm = $this->createForm(ComprobanteType::class, $comprobante, array('attr' => array('tipo' => 'Compra')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            //INICIO Validacion Comprobante Existente
            $comprobantesDuplicado = $em->getRepository('AppBundle:Comprobante')->findBy(Array('proveedor' => $comprobante->getProveedor(), 'puntoVenta' => $comprobante->getPuntoVenta(), 'numero' => $comprobante->getNumero(), 'tipo' => $comprobante->getTipo(), 'activo'=>1, 'movimiento' => 'Compra'));

            foreach($comprobantesDuplicado as $comprobanteDuplicado) {
                if ($comprobante->getId() != $comprobanteDuplicado->getId()) {
                    $this->get('session')->getFlashbag()->add('warning', 'El Comprobante ya fue cargado');

                    return $this->render('comprobantecompra/edit.html.twig', array(
                        'comprobante' => $comprobante,
                        'edit_form' => $editForm->createView(),
                    ));
                }
            }
            //FIN Validacion Comprobante Existente

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
        
            $comprobante->setSucursal($sucursal);
            $comprobante->setPendiente($comprobante->getTotal());
            $comprobante->setUsuario($this->getUser());

            //**********************************************************************
            //ESTA parte es para que funcione el delete de articulos.
            //Basicamente seteo a todos los articulos del comprobante activos en la base
            //de datos con Activo = 0
            $comprobanteDetalleDelete = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findBy(array('comprobante' => $comprobante, 'activo' => true));

            foreach ($comprobanteDetalleDelete as $comprobanteDetalle) {
                $comprobanteDetalle->setActivo(0);
            }   
            //**********************************************************************

            //dump($editForm->getData()->getComprobanteDetalles());
            //die;
            foreach($editForm->getData()->getComprobanteDetalles() as $comprobanteDetalle) {
                //Verifico si hay que agregar el artículo ingresado
                $crear_articulo = false;
                if (is_null($comprobanteDetalle->getArticulo())) {
                    $crear_articulo = true;
                }
                elseif (is_null($comprobanteDetalle->getArticulo()->getId())) {
                    $crear_articulo = true;
                }
                if ($crear_articulo) {
                    $articulo = new Articulo();

                    $categoria = $em->getRepository('AppBundle:ArticuloCategoria')->find(1);
                    $marca = $em->getRepository('AppBundle:ArticuloMarca')->find(1);
                    $iva_21 = $em->getRepository('AppBundle:AfipAlicuota')->findOneBy(array('descripcion' => '21.00'));

                    $articulo->setCodigo('S/A');
                    $articulo->setCategoria($categoria);
                    $articulo->setMarca($marca);
                    $articulo->setDescripcion($comprobanteDetalle->getObservaciones());
                    $articulo->setIva($iva_21); //Asigno 21% de iva, después habría si es correcto
                    $articulo->setForma('');
                    $articulo->setTipoAro('');
                    $articulo->setColorMarco('');
                    $articulo->setColorCristal('');
                    $articulo->setActivo(true);
                    $articulo->setPrecioModifica(1);
                    $articulo->setOrdenTrabajo(1);
                    $articulo->setUltimoComprobante($comprobante);
                    $articulo->setCreatedBy($this->getUser());
                    $articulo->setCreatedAt(new \DateTime("now"));
                    $articulo->setUpdatedBy($this->getUser());
                    $articulo->setUpdatedAt(new \DateTime("now"));

                    $em->persist($articulo);

                    $sucursales = $em->getRepository('AppBundle:Sucursal')->findBy(array('activo' => true));

                    foreach ($sucursales as $sucursal) {
                        $stock = new Stock();

                        $stock->setArticulo($articulo);
                        $stock->setSucursal($sucursal);
                        $stock->setCantidad(0);
                        $stock->setCantidadMinima(1);
                        $stock->setActivo(true);
                        $stock->setCreatedBy($this->getUser());
                        $stock->setCreatedAt(new \DateTime("now"));
                        $stock->setUpdatedBy($this->getUser());
                        $stock->setUpdatedAt(new \DateTime("now"));

                        $em->persist($stock);
                    }

                    $comprobanteDetalle->setArticulo($articulo);
                }

                $comprobanteDetalle->setPrecioNeto(0);
                $comprobanteDetalle->setImporteGanancia(0);
                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);

                if (is_null($comprobanteDetalle->getObservaciones())) {
                    $comprobanteDetalle->setObservaciones('');
                }

                $comprobanteDetalle->setTotalNeto($comprobanteDetalle->getPrecioCosto()*$comprobanteDetalle->getCantidad());
                
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setMovimiento('Compra');
                $comprobanteDetalle->setActivo(1);
                $comprobanteDetalle->setCreatedBy($this->getUser());
                $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                $comprobanteDetalle->setUpdatedBy($this->getUser());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));

                // Actualizacion Stock
                if (!isset($stockComprobante[$comprobanteDetalle->getArticulo()->getId()])) {
                    //Si se agregó un item nuevo
                    $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

                    $stockComprobante[$comprobanteDetalle->getArticulo()->getId()] = $stock;
                }
                
                if (!$crear_articulo) {
                    if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                        $cantidadActual = $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->getCantidad() + $comprobanteDetalle->getCantidad();
                    }
                    else {
                        $cantidadActual = $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->getCantidad() - $comprobanteDetalle->getCantidad();
                    }

                    $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setCantidad($cantidadActual);
                    $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setUpdatedBy($this->getUser());
                    $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setUpdatedAt(new \DateTime("now"));
                }

                if (is_null($comprobanteDetalle->getId())){     
                    if (is_null($comprobanteDetalle->getObservaciones())) {
                        $comprobanteDetalle->setObservaciones('');
                    }

                    $comprobanteDetalle->setCreatedBy($this->getUser());
                    $comprobanteDetalle->setCreatedAt(new \DateTime("now"));    
                    
                    $em->persist($comprobanteDetalle);
                }

                //Actualizo datos en el artículo, solo si corresponde al último ingreso del artículo
                $articulo = $comprobanteDetalle->getArticulo();

                $iva = $articulo->getIva()->getDescripcion();
                //calculo el precio con iva sin el porcentaje del 15% de tarjeta
                $articulo_precio_venta_sin_tarjeta = 100 * $comprobanteDetalle->getPrecioVenta() / 115;
                
                //calculo el precio sin iva
                $articulo_precio_venta_sin_iva = 100 * $articulo_precio_venta_sin_tarjeta / (100+$iva);

                if (!is_null($articulo->getUltimoComprobante())) {
                    if ($articulo->getUltimoComprobante()->getId() == $comprobante->getId()) {
                        $articulo->setPrecioCosto($comprobanteDetalle->getPrecioCosto());
                        $articulo->setGananciaPorcentaje($comprobanteDetalle->getPorcentajeGanancia());
                        $articulo->setPrecioVentaSinIva($articulo_precio_venta_sin_iva);
                        $articulo->setPrecioVenta($comprobanteDetalle->getPrecioVenta());
                    }
                }
            }

            //Actualizo el saldo del proveedor
            $proveedor = $comprobante->getProveedor();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal() + $comprobante_saldo_anterior;
            }
            else {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal() - $comprobante_saldo_anterior;
            }
            $proveedor->setSaldo($proveedor_saldo_actualizado);
            $comprobante->setSaldo($proveedor_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobantecompra/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a comprobante entity.
     *
     */
    public function deleteAction(Request $request, Comprobante $comprobante)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteCompra', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        //$comprobante = $em->getRepository('AppBundle:Comprobante')->find($id);
        if ($comprobante->getActivo() > 0)
            $comprobante->setActivo(0);
        else
            $comprobante->setActivo(1);  

        $comprobante->setUpdatedBy($this->getUser()); 
        $comprobante->setUpdatedAt(new \DateTime("now"));

        //Actualizo el saldo del proveedor
        $proveedor = $comprobante->getProveedor();
        if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal();
        }
        else {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal();
        }
        $proveedor->setSaldo($proveedor_saldo_actualizado);
        
        $em->flush();
        
        return $this->redirectToRoute('comprobanteventa_index');
    }

    public function findSelect2Action(Request $request) {
        $em = $em = $this->getDoctrine()->getManager('default');
        
        $text_search = $request->get('q');
        $pageLimit = $request->get('page_limit');

        if (!is_numeric($pageLimit) || $pageLimit > 10) {
            $pageLimit = 10;
        }

        //return $this->tipo->getDescripcionCorta().': '.$this->puntoVenta.'-'.$this->afipNumero.'(N° Int: '.$this->numero.')';

        $result = $em->createQuery("
                        SELECT r.id as id, CONCAT(t.descripcionCorta, ': ', r.puntoVenta, '-', r.numero, ' (', p.nombre, ')') as text
                        FROM AppBundle:Comprobante r
                        INNER JOIN r.proveedor p
                        INNER JOIN r.tipo t
                        WHERE r.activo = 1 
                        AND (p.nombre LIKE :text_search OR r.numero LIKE :text_search)
                        AND r.movimiento = 'Compra'
                        ORDER BY r.id ASC
                        ")
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }

    /**
     * Creates a form to delete a comprobante entity.
     *
     * @param Comprobante $comprobante The comprobante entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comprobante $comprobante)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comprobantecompra_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
