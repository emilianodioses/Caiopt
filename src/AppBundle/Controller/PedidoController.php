<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use AppBundle\Entity\Pedido;
use AppBundle\Entity\PedidoDetalle;
use AppBundle\Entity\Articulo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Form\PedidoType;
use AppBundle\Form\PedidoDetalleType;

/**
 * Pedido controller.
 *
 */
class PedidoController extends controller
{
    /**
     * Lists all pedido entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $pedidos = $em->getRepository('AppBundle:Pedido')->findByTexto($this->getUser()->getSucursal()->getId(), $texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $pedidos,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );
        
        return $this->render('pedido/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto,
        ));
    }

    /**
     * Creates a new pedido entity.
     *
     */
    public function newAction(Request $request)
    {
        $pedido = new Pedido();
        $pedido->setFecha(new \DateTime("now"));
        $pedido->setUsuario($this->getUser());
        $form = $this->createForm(PedidoType::class, $pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            $max_numero_pedido = $em->createQueryBuilder()
                 ->select('MAX(c.numero)')
                 ->from('AppBundle:Pedido', 'c')
                 ->getQuery()
                 ->getSingleScalarResult();
            
            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            $pedido->setNumero($max_numero_pedido+1);
            $pedido->setSucursal($sucursal);
            $pedido->setUsuario($this->getUser());
            $pedido->setActivo(1);

            if (is_null($pedido->getObservaciones())) {
                $pedido->setObservaciones('');
            }

            $pedido->setCreatedBy($this->getUser());
            $pedido->setCreatedAt(new \DateTime("now"));
            $pedido->setUpdatedBy($this->getUser());
            $pedido->setUpdatedAt(new \DateTime("now"));

            $em->persist($pedido);

            $pedidoDetalle = new PedidoDetalle();
            $pedidoDetalles  = $pedido->getPedidoDetalles()->toArray();

            foreach($pedidoDetalles as $pedidoDetalle) {
                //Verifico si hay que agregar el artículo ingresado 
                if (is_null($pedidoDetalle->getArticulo()->getId())) {
                    $articulo = new Articulo();

                    $categoria = $em->getRepository('AppBundle:ArticuloCategoria')->find(1);
                    $marca = $em->getRepository('AppBundle:ArticuloMarca')->find(1);
                    $iva_21 = $em->getRepository('AppBundle:AfipAlicuota')->findOneBy(array('descripcion' => '21.00'));

                    $articulo->setCodigo('S/A');
                    $articulo->setCategoria($categoria);
                    $articulo->setMarca($marca);
                    $articulo->setDescripcion($pedidoDetalle->getArticulo()->getDescripcion());
                    $articulo->setIva($iva_21); //Asigno 21% de iva, después habría si es correcto
                    $articulo->setForma('');
                    $articulo->setTipoAro('');
                    $articulo->setColorMarco('');
                    $articulo->setColorCristal('');
                    $articulo->setActivo(true);
                    $articulo->setPrecioModifica(1);
                    $articulo->setOrdenTrabajo(1);
                    $articulo->setUltimoPedido(null);
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

                    $pedidoDetalle->setArticulo($articulo);
                }


                $pedidoDetalle->setPedido($pedido);
                $pedidoDetalle->setActivo(1);
                $pedidoDetalle->setCreatedBy($this->getUser());
                $pedidoDetalle->setCreatedAt(new \DateTime("now"));
                $pedidoDetalle->setUpdatedBy($this->getUser());
                $pedidoDetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($pedidoDetalle);
            }

            $em->flush();
            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        return $this->render('pedido/new.html.twig', array(
            'pedido' => $pedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pedido entity.
     *
     */
    public function showAction(Pedido $pedido)
    {
        $em = $this->getDoctrine()->getManager();
        $pedidoDetalles = $em->getRepository('AppBundle:PedidoDetalle')->findBy(Array('pedido'=>$pedido,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($pedido);

        return $this->render('pedido/show.html.twig', array(
            'pedido' => $pedido,
            'pedidodetalles' => $pedidoDetalles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pedido entity.
     *
     */
    public function editAction(Request $request, Pedido $pedido)
    {
        $em = $this->getDoctrine()->getManager();
         
        //Solo puede editarse si la sucursal elegida es la misma del pedido.
        if ($pedido->getSucursal()->getId() != $this->getUser()->getSucursal()->getId()) {

            $this->get('session')->getFlashbag()->add('warning', 'Pedido de sucursal: '.$pedido->getSucursal().'. La sucursal actual es: '.$this->getUser()->getSucursal().', Cambie de sucursal para editar el registro');

            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        $pedidoDetalles = $em->getRepository('AppBundle:PedidoDetalle')->findBy(Array('pedido'=>$pedido, 'activo' => 1));

        if (is_null($pedido->getObservaciones())) {
            $pedido->setObservaciones('');
        }

        foreach($pedidoDetalles as $pedidoDetalle) {
            $pedido->getPedidoDetalles()->add($pedidoDetalle);
        }

        $editForm = $this->createForm(PedidoType::class, $pedido);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (is_null($pedido->getObservaciones())) {
                $pedido->setObservaciones('');
            }

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
            $pedido->setSucursal($sucursal);

            //**********************************************************************
            //ESTA parte es para que funcione el delete de articulos.
            //Basicamente seteo a todos los articulos del pedido activos en la base
            //de datos con Activo = 0
            $pedidoDetalleDelete = $em->getRepository('AppBundle:PedidoDetalle')
                    ->findBy(array('pedido' => $pedido, 'activo' => true));

            foreach ($pedidoDetalleDelete as $pedidoDetalle) {
                $pedidoDetalle->setActivo(0);
            }   
            //**********************************************************************

            foreach($editForm->getData()->getPedidoDetalles() as $pedidoDetalle) {
                $pedidoDetalle->setPedido($pedido);
                $pedidoDetalle->setActivo(1);
                $pedidoDetalle->setCreatedBy($this->getUser());
                $pedidoDetalle->setCreatedAt(new \DateTime("now"));
                $pedidoDetalle->setUpdatedBy($this->getUser());
                $pedidoDetalle->setUpdatedAt(new \DateTime("now"));

                if (is_null($pedidoDetalle->getId())){     
                    $pedidoDetalle->setCreatedBy($this->getUser());
                    $pedidoDetalle->setCreatedAt(new \DateTime("now"));    
                    
                    $em->persist($pedidoDetalle);
                }
            }

            $em->flush();

            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        return $this->render('pedido/edit.html.twig', array(
            'pedido' => $pedido,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a pedido entity.
     *
     */
    public function deleteAction(Request $request, Pedido $pedido)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Pedido', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        //$pedido = $em->getRepository('AppBundle:Pedido')->find($id);
        if ($pedido->getActivo() > 0)
            $pedido->setActivo(0);
        else
            $pedido->setActivo(1);  

        $pedido->setUpdatedBy($this->getUser()); 
        $pedido->setUpdatedAt(new \DateTime("now"));

        //Actualizo el saldo del proveedor
        $proveedor = $pedido->getProveedor();
        if (strpos($pedido->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() + $pedido->getTotal();
        }
        else {
            $proveedor_saldo_actualizado = $proveedor->getSaldo() - $pedido->getTotal();
        }
        $proveedor->setSaldo($proveedor_saldo_actualizado);
        
        $em->flush();
        
        return $this->redirectToRoute('pedido_index');
    }

    /**
     * Creates a form to delete a pedido entity.
     *
     * @param Pedido $pedido The pedido entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pedido $pedido)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pedido_delete', array('id' => $pedido->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Imprime la factura electrónica generada por medio de WS con afip.
     *
     */
    public function imprimirAction(Request $request, Pedido $pedido)
    {
        $em = $this->getDoctrine()->getManager();

        $pedidoDetalles = $em->getRepository('AppBundle:PedidoDetalle')->findBy(Array('pedido'=>$pedido,  'activo'=>1));

        $facturaTemplate = 'pedido/imprimir.html.twig';
    
        $html = $this->renderView($facturaTemplate, array(
            'pedido' => $pedido,
            'pedidoDetalles' => $pedidoDetalles,
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
        
        $filename = 'Pedido_'.$pedido->getNumero();
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }
}
