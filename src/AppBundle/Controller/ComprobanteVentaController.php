<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use AppBundle\Entity\OrdenTrabajo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ComprobanteType;
use AppBundle\Form\ComprobanteDetalleType;
use AppBundle\Form\OrdenTrabajoType;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Services\Mail;
use Symfony\Component\HttpFoundation\Response;


/**
 * Comprobante controller.
 *
 */
class ComprobanteVentaController extends Controller
{
    /**
     * Lists all comprobante entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findByTexto_ventas($this->getUser()->getSucursal()->getId(), $texto);

        return $this->render('comprobanteventa/index.html.twig', array(
            'comprobantes' => $comprobantes,
            'texto' => $texto,
        ));
    }

    /**
     * Creates a new comprobante entity.
     *
     */
    public function newAction(Request $request, $id, $tipo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $comprobante = new Comprobante();

        //Agrego Valor Default dia de la fecha a la fecha de venta
        $comprobante->setFecha(new \DateTime("now"));
        $comprobante->setUsuario($this->getUser());

        //Si el id no es 0 asigno al comprobante de venta la orden de trabajo pasada por parametro y los articulos vinculados a ella.
        $em = $this->getDoctrine()->getManager();
        if($id > 0)
        {
            if($tipo != "contactologia"){
                $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($id); 
                $comprobante->setOrdenTrabajo($ordenTrabajo);      
                $comprobante->setCliente($ordenTrabajo->getCliente());
                $comprobante->setObraSocialPlan($ordenTrabajo->getObraSocialPlan());
                $comprobante->setUsuario($ordenTrabajo->getUsuario());

                $ordenTrabajoDetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(array('ordenTrabajo' => $ordenTrabajo));

                foreach($ordenTrabajoDetalles as $ordenTrabajoDetalle) {  
                    $comprobanteDetalle = new ComprobanteDetalle();
                    $articulo = $em->getRepository('AppBundle:Articulo')->find($ordenTrabajoDetalle->getArticulo());

                    $importe_iva = $ordenTrabajoDetalle->getTotal() * floatval($articulo->getIva()->getDescripcion()) / (100 + floatval($articulo->getIva()->getDescripcion()));

                    $precio_unitario = $ordenTrabajoDetalle->getTotal() - $importe_iva;

                    $comprobanteDetalle->setMovimiento('Venta');
                    $comprobanteDetalle->setArticulo($articulo);
                    $comprobanteDetalle->setCantidad(1);
                    $comprobanteDetalle->setPrecioVenta($ordenTrabajoDetalle->getTotal());
                    $comprobanteDetalle->setPorcentajeBonificacion('0');
                    $comprobanteDetalle->setImporteBonificacion('0');
                    $comprobanteDetalle->setPorcentajeIva($articulo->getIva()->getDescripcion());
                    $comprobanteDetalle->setImporteIva($importe_iva);
                    $comprobanteDetalle->setPrecioUnitario($precio_unitario);
                    $comprobanteDetalle->setTotal($ordenTrabajoDetalle->getTotal());

                    $comprobante->getComprobanteDetalles()->add($comprobanteDetalle);
                }
            }
            else
            {
                $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($id); 
                $comprobante->setOrdenTrabajoContactologia($ordenTrabajoContactologia);      
                $comprobante->setCliente($ordenTrabajoContactologia->getCliente());
                $comprobante->setObraSocialPlan($ordenTrabajoContactologia->getObraSocialPlan());
                $comprobante->setUsuario($ordenTrabajoContactologia->getUsuario());

                $ordenTrabajoContactologiaDetalles = $em->getRepository('AppBundle:OrdenTrabajoContactologiaDetalle')->findBy(array('ordenTrabajoContactologia' => $ordenTrabajoContactologia));

                foreach($ordenTrabajoContactologiaDetalles as $ordenTrabajoContactologiaDetalle) {  
                    $comprobanteDetalle = new ComprobanteDetalle();
                    $articulo = $em->getRepository('AppBundle:Articulo')->find($ordenTrabajoContactologiaDetalle->getArticulo());

                    $importe_iva = $ordenTrabajoContactologiaDetalle->getTotal() * floatval($articulo->getIva()->getDescripcion()) / (100 + floatval($articulo->getIva()->getDescripcion()));

                    $precio_unitario = $ordenTrabajoContactologiaDetalle->getTotal() - $importe_iva;

                    $comprobanteDetalle->setMovimiento('Venta');
                    $comprobanteDetalle->setArticulo($articulo);
                    $comprobanteDetalle->setCantidad(1);
                    $comprobanteDetalle->setPrecioVenta($ordenTrabajoContactologiaDetalle->getTotal());
                    $comprobanteDetalle->setPorcentajeBonificacion('0');
                    $comprobanteDetalle->setImporteBonificacion('0');
                    $comprobanteDetalle->setPorcentajeIva($articulo->getIva()->getDescripcion());
                    $comprobanteDetalle->setImporteIva($importe_iva);
                    $comprobanteDetalle->setPrecioUnitario($precio_unitario);
                    $comprobanteDetalle->setTotal($ordenTrabajoContactologiaDetalle->getTotal());

                    $comprobante->getComprobanteDetalles()->add($comprobanteDetalle);
                }
            }
        }

        $form = $this->createForm(ComprobanteType::class, $comprobante, array('attr' => array('tipo' => 'Venta')));

        $form->handleRequest($request);        

        if ($form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();

            $max_numero_comprobante = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:Comprobante', 'c')
             ->where('c.movimiento = :venta')
             ->setParameter('venta', 'venta')
             ->getQuery()
             ->getSingleScalarResult();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());

            $comprobante->setClienteRazonSocial($comprobante->getCliente()->getNombre());
            $comprobante->setClienteDocumentoTipo($comprobante->getCliente()->getDocumentoTipo());
            $comprobante->setClienteDocumentoNumero($comprobante->getCliente()->getDocumentoNumero());
            $comprobante->setClienteDomicilio($comprobante->getCliente()->getDireccion());
            $comprobante->setClienteLocalidad($comprobante->getCliente()->getLocalidad()->getNombre());
            $comprobante->setClienteIvaCondicion($comprobante->getCliente()->getIvaCondicion()->getDescripcion());
            
            $comprobante->setPuntoVenta($comprobante->getPuntoVentaId()->getNumero());
            $comprobante->setSucursal($sucursal);
            $comprobante->setNumero($max_numero_comprobante+1);
            $comprobante->setMovimiento('Venta');
            $comprobante->setPendiente($comprobante->getTotal());
            //$comprobante->setObraSocial($comprobante->getObraSocialPlan()->getObraSocial());
            $comprobante->setActivo(1);
            $comprobante->setCreatedBy($this->getUser());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser());
            $comprobante->setUpdatedAt(new \DateTime("now"));


            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $em->persist($comprobante);

            $comprobanteDetalles = $comprobante->getComprobanteDetalles()->toArray();

            $orden_trabajo_crear = false;

            foreach($comprobanteDetalles as $comprobanteDetalle) {  
                $articulo = $em->getRepository('AppBundle:Articulo')->find($comprobanteDetalle->getArticulo());

                $comprobanteDetalle->setMovimiento('Venta');
                $comprobanteDetalle->setUpdatedBy($this->getUser());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setActivo(1);

                $comprobanteDetalle->setPrecioCosto($articulo->getPrecioCosto());

                $comprobanteDetalle->setPrecioNeto(($comprobanteDetalle->getPrecioUnitario()-$comprobanteDetalle->getImporteBonificacion()));

                $comprobanteDetalle->setImporteGanancia(
                    ($comprobanteDetalle->getPrecioNeto()-
                    $comprobanteDetalle->getPrecioCosto())*$comprobanteDetalle->getCantidad());
                
                $comprobanteDetalle->setImporteBonificacion($comprobanteDetalle->getCantidad()*($comprobanteDetalle->getporcentajeBonificacion()/100*$comprobanteDetalle->getPrecioUnitario()));

                $comprobanteDetalle->setTotalNeto(($comprobanteDetalle->getPrecioUnitario()*$comprobanteDetalle->getCantidad()-$comprobanteDetalle->getImporteBonificacion()));

                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);

                $comprobanteDetalle->setImporteIva($comprobanteDetalle->getTotalNeto()*$comprobanteDetalle->getPorcentajeIva()/100);

                if (is_null($comprobanteDetalle->getObservaciones())) {
                    $comprobanteDetalle->setObservaciones('');
                }

                $comprobanteDetalle->setPorcentajeGanancia(0); //según el comentario en la entidad este campo no se utiliza en la venta
                //$comprobanteDetalle->setPorcentajeGanancia((($comprobanteDetalle->getPrecioNeto()/$comprobanteDetalle->getPrecioCosto())-1)*100);

                $comprobanteDetalle->setMovimiento('Venta');
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setActivo(1);
                $comprobanteDetalle->setCreatedBy($this->getUser());
                $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                $comprobanteDetalle->setUpdatedBy($this->getUser());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));

                // Actualizacion Stock
                $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

                if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                    $cantidad = $stock->getCantidad() - $comprobanteDetalle->getCantidad();
                }
                else {
                    $cantidad = $stock->getCantidad() + $comprobanteDetalle->getCantidad();
                }
                $stock->setCantidad($cantidad);
                $stock->setUpdatedBy($this->getUser());
                $stock->setUpdatedAt(new \DateTime("now"));
                $em->persist($stock);

                $em->persist($comprobanteDetalle);
            }

            //Si se eligio una orden de trabajo asociada al comprobante, actualizo la orden de Trabajo para que tambien este 
            //vinculada al comprobante
            if (!is_null($comprobante->getOrdenTrabajo())) {

                $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($comprobante->getOrdenTrabajo()->getId());
                $ordenTrabajo->setComprobante($comprobante);                
            }
            if (!is_null($comprobante->getOrdenTrabajoContactologia())) {

                $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($comprobante->getOrdenTrabajoContactologia()->getId());
                $ordenTrabajoContactologia->setComprobante($comprobante);                
            }

            //Actualizo el saldo del cliente
            $cliente = $comprobante->getCliente();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $cliente_saldo_actualizado = $cliente->getSaldo() - $comprobante->getTotal();
            }
            else {
                $cliente_saldo_actualizado = $cliente->getSaldo() + $comprobante->getTotal();
            }
            $cliente->setSaldo($cliente_saldo_actualizado);
            $comprobante->setSaldo($cliente_saldo_actualizado);
            //Actualizo la obra social - plan del cliente
            $cliente->setObraSocialPlan($comprobante->getObraSocialPlan());
            $cliente->setUpdatedBy($this->getUser());
            $cliente->setUpdatedAt(new \DateTime("now"));

            
            $em->flush();

            $this->get('session')->getFlashbag()->add('success', 'Venta realizada exitosamente.');
            
            return $this->redirectToRoute('comprobanteventa_show', 
                array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/new.html.twig', array(
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
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $reciboComprobantes = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobanteventa/show.html.twig', array(
            'comprobante' => $comprobante,
            'comprobanteDetalles' => $comprobanteDetalles,
            'reciboComprobantes' => $reciboComprobantes,
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
        
        //Guardo el saldo del cliente antes de editar el comprobante
        $comprobante_saldo_anterior = $comprobante->getTotal();
        $cantidadVieja = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(array('comprobante' => $comprobante))[0]->getCantidad();
        
        //Valido si el Comprobante fue Facturado, en dicho caso redirect a show
        if (!(is_null($comprobante->getCaeNumero()))) {

            $this->get('session')->getFlashbag()->add('warning', 'El comprobante ya fue facturado. Edición denegada.');

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        //Solo puede editarse si la sucursal elegida es la misma del comprobante.
        if ($comprobante->getSucursal()->getId() != $this->getUser()->getSucursal()->getId()) {

            $this->get('session')->getFlashbag()->add('warning', 'Comprobante de sucursal: '.$comprobante->getSucursal().'. La sucursal actual es: '.$this->getUser()->getSucursal().', Cambie de sucursal para editar el registro');

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        //Si ya tiene recibos asociados no se puede modificar
        $recibos = $em->getRepository('AppBundle:ReciboComprobante')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));
        if (count($recibos) > 0) {
            $this->get('session')->getFlashbag()->add('warning', 'El comprobante ya tiene recibos asociados. Edición denegada.');

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        $stockComprobante = array();
        foreach($comprobanteDetalles as $comprobanteDetalle) {
            $comprobante->getComprobanteDetalles()->add($comprobanteDetalle);

            //Guardo el stock de cada artículo devolviendo el stock por si llegase a eliminarse algún item
            $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $cantidad = $stock->getCantidad() + $comprobanteDetalle->getCantidad();
            }
            else {
                $cantidad = $stock->getCantidad() - $comprobanteDetalle->getCantidad();
            }
            $stock->setCantidad($cantidad);
            $stockComprobante[$comprobanteDetalle->getArticulo()->getId()] = $stock;
        }

        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante, array('attr' => array('tipo' => 'Venta')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
            $comprobante->setClienteRazonSocial($comprobante->getCliente()->getNombre());
            $comprobante->setClienteDocumentoTipo($comprobante->getCliente()->getDocumentoTipo()->getDescripcion());
            $comprobante->setClienteDocumentoNumero($comprobante->getCliente()->getDocumentoNumero());
            $comprobante->setClienteDomicilio($comprobante->getCliente()->getDireccion());
            $comprobante->setClienteLocalidad($comprobante->getCliente()->getLocalidad()->getNombre());
            $comprobante->setClienteIvaCondicion($comprobante->getCliente()->getIvaCondicion()->getDescripcion());

            $comprobante->setPuntoVenta($comprobante->getPuntoVentaId()->getNumero());
            $comprobante->setSucursal($sucursal);
            $comprobante->setPendiente($comprobante->getTotal());
            //$comprobante->setObraSocial($comprobante->getObraSocialPlan()->getObraSocial());


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

            $comprobanteDetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobanteDetalles as $comprobanteDetalle) {
                $articulo = $em->getRepository('AppBundle:Articulo')->find($comprobanteDetalle->getArticulo());

                $comprobanteDetalle->setMovimiento('Venta');
                $comprobanteDetalle->setUpdatedBy($this->getUser());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setActivo(1);

                $comprobanteDetalle->setPrecioCosto($articulo->getPrecioCosto());

                $comprobanteDetalle->setPrecioNeto(($comprobanteDetalle->getPrecioUnitario()-$comprobanteDetalle->getImporteBonificacion()));

                $comprobanteDetalle->setImporteGanancia(
                    ($comprobanteDetalle->getPrecioNeto()-
                    $comprobanteDetalle->getPrecioCosto())*$comprobanteDetalle->getCantidad());
                
                $comprobanteDetalle->setImporteBonificacion($comprobanteDetalle->getCantidad()*($comprobanteDetalle->getporcentajeBonificacion()/100*$comprobanteDetalle->getPrecioUnitario()));

                $comprobanteDetalle->setTotalNeto(($comprobanteDetalle->getPrecioUnitario()*$comprobanteDetalle->getCantidad()-$comprobanteDetalle->getImporteBonificacion()));

                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);

                $comprobanteDetalle->setImporteIva($comprobanteDetalle->getTotalNeto()*$comprobanteDetalle->getPorcentajeIva()/100);

                if (is_null($comprobanteDetalle->getObservaciones())) {
                    $comprobanteDetalle->setObservaciones('');
                }

                $comprobanteDetalle->setPorcentajeGanancia(0); //según el comentario en la entidad este campo no se utiliza en la venta
                //$comprobanteDetalle->setPorcentajeGanancia((($comprobanteDetalle->getPrecioNeto()/$comprobanteDetalle->getPrecioCosto())-1)*100);

                if (is_null($comprobanteDetalle->getId())){     
                    $comprobanteDetalle->setCreatedBy($this->getUser());
                    $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                    $em->persist($comprobanteDetalle);
                }

                // Actualizacion Stock
                if (!isset($stockComprobante[$comprobanteDetalle->getArticulo()->getId()])) {
                    //Si se agregó un item nuevo
                    $stock = $em->getRepository('AppBundle:Stock')->findOneBy(array('sucursal' => $this->getUser()->getSucursal(), 'articulo' => $comprobanteDetalle->getArticulo()));

                    $stockComprobante[$comprobanteDetalle->getArticulo()->getId()] = $stock;
                }

                if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                    $cantidadActual = $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->getCantidad() - $comprobanteDetalle->getCantidad();
                }
                else {
                    $cantidadActual = $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->getCantidad() + $comprobanteDetalle->getCantidad();
                }

                $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setCantidad($cantidadActual);
                $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setUpdatedBy($this->getUser());
                $stockComprobante[$comprobanteDetalle->getArticulo()->getId()]->setUpdatedAt(new \DateTime("now"));
            }

            //Actualizo el saldo del cliente
            $cliente = $comprobante->getCliente();
            if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
                $cliente_saldo_actualizado = $cliente->getSaldo() - $comprobante->getTotal() + $comprobante_saldo_anterior;
            }
            else {
                $cliente_saldo_actualizado = $cliente->getSaldo() + $comprobante->getTotal() - $comprobante_saldo_anterior;
            }
            $cliente->setSaldo($cliente_saldo_actualizado);
            $comprobante->setSaldo($cliente_saldo_actualizado);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comprobante entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'Delete', $this->getUser()->getRol())):
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

        //Si se eligio una orden de trabajo asociada al comprobante, actualizo la orden de Trabajo para que tambien este 
        //vinculada al comprobante
        if (!is_null($comprobante->getOrdenTrabajo())) {

            $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($comprobante->getOrdenTrabajo()->getId());
            $ordenTrabajo->setComprobante(NULL);
        }
        if (!is_null($comprobante->getOrdenTrabajoContactologia())) {

            $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($comprobante->getOrdenTrabajoContactologia()->getId());
            $ordenTrabajoContactologia->setComprobante(NULL);
        }

        //Actualizo el saldo del cliente
        $cliente = $comprobante->getCliente();
        if (strpos($comprobante->getTipo()->getDescripcion(), 'NOTA DE CREDITO') === false) {
            $cliente_saldo_actualizado = $cliente->getSaldo() + $comprobante->getTotal();
        }
        else {
            $cliente_saldo_actualizado = $cliente->getSaldo() - $comprobante->getTotal();
        }
        $cliente->setSaldo($cliente_saldo_actualizado);
        
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
            ->setAction($this->generateUrl('comprobanteventa_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Genera la factura electrónica por medio de WS con afip.
     *
     */
    public function facturarAction(Request $request, Comprobante $comprobante)
    {
        if (!$comprobante->getPuntoVentaId()->getFeHabilitada()) {
            $this->get('session')->getFlashbag()->add('warning', 'El punto de venta del comprobante no se encuentra habilitado para emitir Factura Electrónica');
            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        $comprobante->setClienteRazonSocial($comprobante->getCliente()->getNombre());
        $comprobante->setClienteDocumentoTipo($comprobante->getCliente()->getDocumentoTipo());
        $comprobante->setClienteDocumentoNumero($comprobante->getCliente()->getDocumentoNumero());
        $comprobante->setClienteDomicilio($comprobante->getCliente()->getDireccion());
        $comprobante->setClienteLocalidad($comprobante->getCliente()->getLocalidad()->getNombre());
        $comprobante->setClienteIvaCondicion($comprobante->getCliente()->getIvaCondicion()->getDescripcion());

        $em = $this->getDoctrine()->getManager();
        $afip = $this->get('AfipFE');

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        //Lo siguiente debería modificarse para que no quede hardcodeado
        //dump($afip->getWS()->ElectronicBilling->GetVoucherTypes());
        //die;
        $comprobanteTipo = $comprobante->getTipo()->getCodigo();

        // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
        $concepto = 1;

        //dump($afip->getWS()->ElectronicBilling->GetDocumentTypes());
        $cliente = $comprobante->getCliente();
        $clienteDocumentoTipo = $cliente->getDocumentoTipo()->getCodigo();

        if ($cliente->getIvaCondicion()->getDescripcion() == 'Consumidor Final' && $cliente->getDocumentoNumero() == 0){
            //Si el cliente es "consumidor final" y no tiene un documento ingresado, se utiliza el nro 99
            $clienteDocumentoNumero = 99;
        }
        else {
            $clienteDocumentoNumero = $cliente->getDocumentoNumero();
        }

        $comprobanteFecha = $comprobante->getFecha()->format('Ymd');
        $comprobanteTotal = $comprobante->getTotal();
        $comprobanteTotalNeto = $comprobante->getTotalNeto();
        $comprobanteImportaIva = $comprobante->getImporteIva();

        $alicuotasIva = $em->getRepository('AppBundle:AfipAlicuota')->findBy(Array('activo'=>1));

        foreach($alicuotasIva as $alicuotaIva) {
            $alicuota['Id'] = $alicuotaIva->getCodigo(); // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
            $alicuota['BaseImp'] = 0; // Base imponible
            $alicuota['Importe'] = 0; // Importe 
            $alicuotas_all[$alicuotaIva->getId()] = $alicuota;
        }

        foreach ($comprobanteDetalles as $cd) {
            $alicuota_id = $em->getRepository('AppBundle:AfipAlicuota')->findOneBy(array('activo'=>1, 'descripcion' => $cd->getPorcentajeIva()))->getId();

            $alicuotas_all[$alicuota_id]['BaseImp'] += $cd->getTotalNeto();
            $alicuotas_all[$alicuota_id]['Importe'] += $cd->getImporteIva(); 

            //dump($alicuotas_all);
            //die;
        }

        foreach ($alicuotas_all as $alicuota) {
            if ($alicuota['Importe'] > 0) {
                $alicuotas[] = $alicuota;
            }
        }

        $data = array(
                'CantReg'   => 1,  // Cantidad de comprobantes a registrar
                'PtoVta'    => $this->getUser()->getSucursal()->getId(),  // Punto de venta
                'CbteTipo'  => $comprobanteTipo,  // Tipo de comprobante (ver tipos disponibles) 
                'Concepto'  => $concepto,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                'DocTipo'   => $clienteDocumentoTipo, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
                'DocNro'    => $clienteDocumentoNumero,  // Número de documento del comprador (0 consumidor final)
                'CbteFch'   => $comprobanteFecha, // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'ImpTotal'  => $comprobanteTotal, // Importe total del comprobante
                'ImpTotConc'    => 0,   // Importe neto no gravado
                'ImpNeto'   => $comprobanteTotalNeto, // Importe neto gravado
                'ImpOpEx'   => 0,   // Importe exento de IVA
                'ImpIVA'    => $comprobanteImportaIva,  //Importe total de IVA
                'ImpTrib'   => 0,   //Importe total de tributos
                'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
                'MonCotiz'  => 1,     // Cotización de la moneda usada (1 para pesos argentinos)
                'Iva'       => $alicuotas, 
            );
        if (!is_null($comprobante->getComprobanteAsociado())) {
            $ca = $comprobante->getComprobanteAsociado();
            $data['CbtesAsoc'] = array( // (Opcional) Comprobantes asociados
                        array(
                            'Tipo'      => $ca->getTipo()->getCodigo(), // Tipo de comprobante (ver tipos disponibles) 
                            'PtoVta'    => $ca->getPuntoVenta(), // Punto de venta
                            'Nro'       => $ca->getAfipNumero() // Numero de comprobante
                            //'Cuit'      => 20111111112 // (Opcional) Cuit del emisor del comprobante
                            //'CbteFch'   => 20191231 // (Opcional) Fecha del comprobante asociado
                            )
                        );
        }

        try {
            $res = $afip->getWS()->ElectronicBilling->CreateNextVoucher($data);
        } catch (\Exception $e) {
            $this->get('session')->getFlashbag()->add('warning', $e->getMessage());
            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }
        
        /*
        $res['CAE']; //CAE asignado el comprobante
        $res['CAEFchVto']; //Fecha de vencimiento del CAE (yyyy-mm-dd)
        $res['voucher_number']; //Número asignado al comprobante
        */

        $comprobante->setCaeNumero($res['CAE']);
        $comprobante->setCaeFechaVencimiento(new \DateTime($res['CAEFchVto']));
        $comprobante->setAfipNumero($res['voucher_number']);

        $em->flush();
        $this->get('session')->getFlashbag()->add('success', 'Venta facturada exitosamente.');
        return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
    }

    /**
     * Imprime la factura electrónica generada por medio de WS con afip.
     *
     */
    public function facturaImprimirAction(Request $request, Comprobante $comprobante)
    {

        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'FacturaImprimir', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        //Si no fue facturado con el WS, solo podemos hacer una impresion interna
        if (is_null($comprobante->getCaeNumero())){
            $facturaTemplate = 'comprobanteventa/factura_imprimir_X.html.twig';
        }
        else {
            $facturaTemplate = 'comprobanteventa/factura_imprimir_'.$comprobante->getTipo()->getLetra().'.html.twig';
        }

        $html = $this->renderView($facturaTemplate, array(
            'comprobante' => $comprobante,
            'comprobanteDetalles' => $comprobanteDetalles,
            'facturaTipo' => substr($comprobante->getTipo(),8,1),
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
        
        $filename = $comprobante->getTipo().' '.$comprobante->getPuntoVenta().'-'.$comprobante->getNumero();
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }

    /**
     * Imprime la factura electrónica generada por medio de WS con afip.
     *
     */
    public function enviarFacturaAction(Request $request, Comprobante $comprobante)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('ComprobanteVenta', 'EnviarFactura', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */

        $em = $this->getDoctrine()->getManager();

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        //Si no fue facturado con el WS, solo podemos hacer una impresion interna
        if (is_null($comprobante->getCaeNumero())){
            $facturaTemplate = 'comprobanteventa/factura_imprimir_X.html.twig';
        }
        else {
            $facturaTemplate = 'comprobanteventa/factura_imprimir_'.$comprobante->getTipo()->getLetra().'.html.twig';
        }

        $html = $this->renderView($facturaTemplate, array(
            'comprobante' => $comprobante,
            'comprobanteDetalles' => $comprobanteDetalles,
            'facturaTipo' => substr($comprobante->getTipo(),8,1),
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

        $folder = $this->container->getParameter('dir_download');

        
        $filename = str_replace('FACTURA ', 'FACTURA_', ($folder. '/' .$comprobante->getCliente().'_'.$comprobante->getPuntoVenta().'_'.$comprobante->getNumero()));
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'F'); // This will output the PDF as a response directly

        $parameters = array('titulo'              => 'Comprobante de Venta',
                            'cliente'             => $comprobante->getCliente()->getEmail(),
                            'nombrecliente'       => $comprobante->getCliente()->getNombre());
        $mailtemplate = 'newemail.html.twig';

        
        $emailResult = Mail::sendEmail($mailtemplate, $parameters, $filename);

        $this->get('session')->getFlashbag()->add('success', 'Comprobante enviado exitosamente.');

        return $this->redirectToRoute('comprobanteventa_show', 
                array('id' => $comprobante->getId()));
    }

}
