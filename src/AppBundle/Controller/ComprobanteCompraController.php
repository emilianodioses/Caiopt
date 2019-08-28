<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Form\ComprobanteType;
use AppBundle\Form\ComprobanteDetalleType;

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
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteCompra', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento'=>'compra', 'activo'=> '1', 'sucursal' => $this->getUser()->getSucursal()));
        
        return $this->render('comprobantecompra/index.html.twig', array(
            'comprobantes' => $comprobantes,
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
            $comprobanteDuplicado = $em->getRepository('AppBundle:Comprobante')->findBy(Array('proveedor' => $comprobante->getProveedor(), 'puntoVenta'=>$comprobante->getPuntoVenta(), 'numero'=>$comprobante->getNumero(),  'activo'=>1, 'movimiento' => 'Compra'));

            if (count($comprobanteDuplicado) > 0) {
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
            $comprobante->setActivo(1);

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $comprobante->setCreatedBy($this->getUser());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser());
            $comprobante->setUpdatedAt(new \DateTime("now"));

            $em->persist($comprobante);

            $comprobantedetalle = new ComprobanteDetalle();
            $comprobantedetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobantedetalles as $comprobantedetalle) {
                $comprobantedetalle->setPrecioNeto(0);
                $comprobantedetalle->setImporteGanancia(0);
                $comprobantedetalle->setTotalNoGravado(0);
                $comprobantedetalle->setImporteIvaExento(0);

                if (is_null($comprobantedetalle->getObservaciones())) {
                    $comprobantedetalle->setObservaciones('');
                }

                $comprobantedetalle->setTotalNeto($comprobantedetalle->getPrecioCosto()*$comprobantedetalle->getCantidad());
                
                $comprobantedetalle->setComprobante($comprobante);
                $comprobantedetalle->setMovimiento('Compra');
                $comprobantedetalle->setActivo(1);
                $comprobantedetalle->setCreatedBy($this->getUser());
                $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                $comprobantedetalle->setUpdatedBy($this->getUser());
                $comprobantedetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($comprobantedetalle);

                //Actualizo datos en el artículo
                $articulo = $comprobantedetalle->getArticulo();

                $iva = $articulo->getIva()->getDescripcion();
                //calculo el precio con iva sin el porcentaje del 15% de tarjeta
                $articulo_precio_venta_sin_tarjeta = 100 * $comprobantedetalle->getPrecioVenta() / 115;
                
                //calculo el precio sin iva
                $articulo_precio_venta_sin_iva = 100 * $articulo_precio_venta_sin_tarjeta / (100+$iva);

                $articulo->setPrecioCosto($comprobantedetalle->getPrecioCosto());
                $articulo->setGananciaPorcentaje($comprobantedetalle->getPorcentajeGanancia());
                $articulo->setPrecioVentaSinIva($articulo_precio_venta_sin_iva);
                $articulo->setPrecioVenta($comprobantedetalle->getPrecioVenta());
                $articulo->setUltimoComprobante($comprobante);
            }

            //Actualizo el saldo del proveedor
            $proveedor = $comprobante->getProveedor();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal();
            }
            else {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal();
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
        $comprobantedetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $ordenPagoComprobantes = $em->getRepository('AppBundle:OrdenPagoComprobante')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobantecompra/show.html.twig', array(
            'comprobante' => $comprobante,
            'comprobantedetalles' => $comprobantedetalles,
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
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        //Guardo el saldo del proveedor antes de editar el comprobante
        $comprobante_saldo_anterior = $comprobante->getTotal();
        
        if (!$secure->isAuthorized('ComprobanteCompra', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

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

        $comprobantedetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        if (is_null($comprobante->getObservaciones())) {
            $comprobante->setObservaciones('');
        }

        foreach($comprobantedetalles as $comprobantedetalle) {
            $comprobante->getComprobanteDetalles()->add($comprobantedetalle);
        }

        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante, array('attr' => array('tipo' => 'Compra')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
        
            $comprobante->setSucursal($sucursal);
            $comprobante->setPendiente($comprobante->getTotal());
            
            //**********************************************************************
            //ESTA parte es para que funcione el delete de articulos.
            //Basicamente seteo a todos los articulos ya existen en la base de datos con 
            //Activo = 0
            $comprobantedetalleDelete = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findBy(array('comprobante' => $comprobante));

            foreach ($comprobantedetalleDelete as $comprobantedetalle) {
                $comprobantedetalle->setActivo(0);
            }   
            //**********************************************************************

            foreach($editForm->getData()->getComprobanteDetalles() as $comprobantedetalle) {
                $comprobantedetalle->setPrecioNeto(0);
                $comprobantedetalle->setImporteGanancia(0);
                $comprobantedetalle->setTotalNoGravado(0);
                $comprobantedetalle->setImporteIvaExento(0);

                if (is_null($comprobantedetalle->getObservaciones())) {
                    $comprobantedetalle->setObservaciones('');
                }

                $comprobantedetalle->setTotalNeto($comprobantedetalle->getPrecioCosto()*$comprobantedetalle->getCantidad());
                
                $comprobantedetalle->setComprobante($comprobante);
                $comprobantedetalle->setMovimiento('Compra');
                $comprobantedetalle->setActivo(1);
                $comprobantedetalle->setCreatedBy($this->getUser());
                $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                $comprobantedetalle->setUpdatedBy($this->getUser());
                $comprobantedetalle->setUpdatedAt(new \DateTime("now"));

                if (is_null($comprobantedetalle->getId())){     
                    if (is_null($comprobantedetalle->getObservaciones())) {
                        $comprobantedetalle->setObservaciones('');
                    }

                    $comprobantedetalle->setCreatedBy($this->getUser());
                    $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                    $em->persist($comprobantedetalle);
                }

                //Actualizo datos en el artículo, solo si corresponde al último ingreso del artículo
                $articulo = $comprobantedetalle->getArticulo();

                $iva = $articulo->getIva()->getDescripcion();
                //calculo el precio con iva sin el porcentaje del 15% de tarjeta
                $articulo_precio_venta_sin_tarjeta = 100 * $comprobantedetalle->getPrecioVenta() / 115;
                
                //calculo el precio sin iva
                $articulo_precio_venta_sin_iva = 100 * $articulo_precio_venta_sin_tarjeta / (100+$iva);

                if (!is_null($articulo->getUltimoComprobante())) {
                    if ($articulo->getUltimoComprobante()->getId() == $comprobante->getId()) {
                        $articulo->setPrecioCosto($comprobantedetalle->getPrecioCosto());
                        $articulo->setGananciaPorcentaje($comprobantedetalle->getPorcentajeGanancia());
                        $articulo->setPrecioVentaSinIva($articulo_precio_venta_sin_iva);
                        $articulo->setPrecioVenta($comprobantedetalle->getPrecioVenta());
                    }
                }
            }

            //Actualizo el saldo del proveedor
            $proveedor = $comprobante->getProveedor();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal() - $comprobante_saldo_anterior;
            }
            else {
                $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal() + $comprobante_saldo_anterior;
            }
            $proveedor->setSaldo($proveedor_saldo_actualizado);
            $comprobante->setSaldo($proveedor_saldo_actualizado);

            $em->flush();

            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobantecompra/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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

        $comprobante = $em->getRepository('AppBundle:Comprobante')->find($id);
        if ($comprobante->getActivo() > 0)
            $comprobante->setActivo(0);
        else
            $comprobante->setActivo(1);  

        $comprobante->setUpdatedBy($this->getUser()); 
        $comprobante->setUpdatedAt(new \DateTime("now"));

        //Actualizo el saldo del proveedor
        $proveedor = $comprobante->getProveedor();
        if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() - $comprobante->getTotal();
        }
        else {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() + $comprobante->getTotal();
        }
        $proveedor->setSaldo($proveedor_saldo_actualizado);
        
        $em->flush($comprobante);
        
        return $this->redirectToRoute('comprobanteventa_index');
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
