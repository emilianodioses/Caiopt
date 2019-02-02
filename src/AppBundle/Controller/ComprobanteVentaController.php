<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use AppBundle\Entity\OrdenTrabajo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ComprobanteType;
use AppBundle\Form\ComprobanteDetalleType;
use AppBundle\Form\OrdenTrabajoType;
use Doctrine\Common\Collections\ArrayCollection;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta', 'activo'=> '1'));

        return $this->render('comprobanteventa/index.html.twig', array(
            'comprobantes' => $comprobantes,
        ));
    }

    /**
     * Creates a new comprobante entity.
     *
     */
    public function newAction(Request $request)
    {
        $comprobante = new Comprobante();
        $form = $this->createForm(ComprobanteType::class, $comprobante);

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

            $comprobante->setNumero($max_numero_comprobante+1);
            $comprobante->setMovimiento('Venta');
            $comprobante->setActivo(1);
            $comprobante->setCreatedBy($this->getUser()->getId());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser()->getId());
            $comprobante->setUpdatedAt(new \DateTime("now"));


            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $em->persist($comprobante);

            $comprobanteDetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobanteDetalles as $comprobanteDetalle) {  
                $articulo = $em->getRepository('AppBundle:Articulo')->find($comprobanteDetalle->getArticulo());

                $alicuotaIva = $em->getRepository('AppBundle:AfipAlicuota')->find($comprobanteDetalle->getArticulo()->getIva()->getId());
                $comprobanteDetalle->setPorcentajeIva($alicuotaIva->getDescripcion());

                $comprobantedetaleBD = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findOneBy(array('articulo' => $articulo)); //xxxx-fr: no se para q es esto

                $comprobanteDetalle->setImporteIva($comprobanteDetalle->getCantidad()*$comprobanteDetalle->getPrecioCosto()*$comprobanteDetalle->getPorcentajeIva()/100);

                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);
                
                if (is_null($comprobanteDetalle->getObservaciones())) {
                $comprobanteDetalle->setObservaciones('');
                }

                $comprobanteDetalle->setPrecioCosto($articulo->getPrecioCosto());
                
                switch ($comprobante->getTipo()) {
                    case 'FACTURA A':
                        $comprobanteDetalle->setPrecioUnitario($comprobantedetaleBD->getPrecioUnitario());
                        break;
                    
                    case 'FACTURA B':
                        $comprobanteDetalle->setPrecioUnitario($comprobantedetaleBD->getPrecioUnitario()*(1+$comprobanteDetalle->getPorcentajeIva()/100));
                        break;
                    
                    default:
                        echo('ComprobanteTipo desconocido.');
                        die;
                        break;
                }



                $comprobanteDetalle->setTotalNeto(($comprobanteDetalle->getPrecioVenta()-$comprobanteDetalle->getImporteBonificacion())*$comprobanteDetalle->getCantidad());

                $comprobanteDetalle->setImporteGanancia(($comprobanteDetalle->getPrecioVenta()-$comprobanteDetalle->getPrecioUnitario())*$comprobanteDetalle->getCantidad());                

                $comprobanteDetalle->setImporteBonificacion($comprobanteDetalle->getCantidad()*($comprobanteDetalle->getporcentajeBonificacion()/100*$comprobanteDetalle->getPrecioCosto()));

                $comprobanteDetalle->setPorcentajeGanancia((($comprobanteDetalle->getPrecioVenta()/$comprobanteDetalle->getPrecioCosto())-1)*100);

                $comprobanteDetalle->setMovimiento('Venta');
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setActivo(1);
                $comprobanteDetalle->setCreatedBy($this->getUser()->getId());
                $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                $comprobanteDetalle->setUpdatedBy($this->getUser()->getId());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($comprobanteDetalle);

                if ($articulo->getOrdenTrabajo()) {
                    $ordenTrabajo = new OrdenTrabajo();

                    //ESTO ESTA MAL, solo lo puse para poner un valor default de taller de estado pendiente
                    $taller = $em->getRepository('AppBundle:Taller')->find(1);
                    $ordenTrabajo->setTaller($taller);

                    $ordenTrabajo->setCliente($comprobante->getCliente());
                    $ordenTrabajo->setComprobante($comprobante);
                    $ordenTrabajo->setEstado('Pendiente');
                                        
                    $ordenTrabajo->setOjoDerechoEje(0);
                    $ordenTrabajo->setOjoDerechoCilindro(0);
                    $ordenTrabajo->setOjoDerechoEsfera(0);
                    $ordenTrabajo->setOjoDerechoAdicc(0);
                    $ordenTrabajo->setOjoDerechoDnp(0);
                    $ordenTrabajo->setOjoDerechoAlt(0);
                    $ordenTrabajo->setOjoIzquierdoEje(0);
                    $ordenTrabajo->setOjoIzquierdoCilindro(0);
                    $ordenTrabajo->setOjoIzquierdoEsfera(0);
                    $ordenTrabajo->setOjoIzquierdoAdicc(0);
                    $ordenTrabajo->setOjoIzquierdoDnp(0);
                    $ordenTrabajo->setOjoIzquierdoAlt(0);
                    $ordenTrabajo->setDip(0);

                    $ordenTrabajo->setActivo(1);
                    $ordenTrabajo->setCreatedBy($this->getUser()->getId());
                    $ordenTrabajo->setCreatedAt(new \DateTime("now"));
                    $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
                    $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

                    $em->persist($ordenTrabajo);
                }
            }

            $em->flush();
            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
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
        $em = $this->getDoctrine()->getManager();
        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobanteventa/show.html.twig', array(
            'comprobante' => $comprobante,
            'comprobanteDetalles' => $comprobanteDetalles,
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

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        foreach($comprobanteDetalles as $comprobanteDetalle) {
            $comprobante->getComprobanteDetalles()->add($comprobanteDetalle);
        }

        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            //**********************************************************************
            //ESTA parte es para que funcione el delete de articulos.
            //Basicamente seteo a todos los articulos ya existen en la base de datos con 
            //Activo = 0
            $comprobanteDetalleDelete = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findBy(array('comprobante' => $comprobante));

            foreach ($comprobanteDetalleDelete as $comprobanteDetalle) {
                $comprobanteDetalle->setActivo(0);
            }   
            //**********************************************************************

            $comprobanteDetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobanteDetalles as $comprobanteDetalle) {
                $articulo = $em->getRepository('AppBundle:Articulo')->find($comprobanteDetalle->getArticulo());

                $comprobantedetaleBD = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findOneBy(array('articulo' => $articulo)); //xxxx-fr: no se para q es esto

                $comprobanteDetalle->setMovimiento('Venta');
                $comprobanteDetalle->setUpdatedBy($this->getUser()->getId());
                $comprobanteDetalle->setUpdatedAt(new \DateTime("now"));
                $comprobanteDetalle->setComprobante($comprobante);
                $comprobanteDetalle->setActivo(1);
                $comprobanteDetalle->setTotalNeto(($comprobanteDetalle->getPrecioVenta()-$comprobanteDetalle->getImporteBonificacion())*$comprobanteDetalle->getCantidad());
                $comprobanteDetalle->setPrecioCosto($articulo->getPrecioCosto());

                switch ($comprobante->getTipo()) {
                    case 'FACTURA A':
                        $comprobanteDetalle->setPrecioUnitario($comprobantedetaleBD->getPrecioUnitario());
                        break;
                    
                    case 'FACTURA B':
                        $comprobanteDetalle->setPrecioUnitario($comprobantedetaleBD->getPrecioVenta()*(1+$comprobanteDetalle->getPorcentajeIva()/100));
                        break;
                    
                    default:
                        echo('ComprobanteTipo desconocido.');
                        die;
                        break;
                }

                $comprobanteDetalle->setImporteGanancia(($comprobanteDetalle->getPrecioVenta()-$comprobanteDetalle->getPrecioUnitario())*$comprobanteDetalle->getCantidad());  
                $comprobanteDetalle->setImporteBonificacion($comprobanteDetalle->getCantidad()*($comprobanteDetalle->getporcentajeBonificacion()/100*$comprobanteDetalle->getPrecioCosto()));
                $comprobanteDetalle->setTotalNoGravado(0);
                $comprobanteDetalle->setImporteIvaExento(0);

                $comprobanteDetalle->setImporteIva($comprobanteDetalle->getCantidad()*$comprobanteDetalle->getPrecioCosto()*$comprobanteDetalle->getPorcentajeIva()/100);

                if (is_null($comprobanteDetalle->getObservaciones())) {
                $comprobanteDetalle->setObservaciones('');
                }

                $comprobanteDetalle->setPorcentajeGanancia((($comprobanteDetalle->getPrecioVenta()/$comprobanteDetalle->getPrecioCosto())-1)*100);

                if (is_null($comprobanteDetalle->getId())){     
                    $comprobanteDetalle->setCreatedBy($this->getUser()->getId());
                    $comprobanteDetalle->setCreatedAt(new \DateTime("now"));
                    $em->persist($comprobanteDetalle);
                }
            }

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
        $em = $this->getDoctrine()->getManager();

        $comprobante = $em->getRepository('AppBundle:Comprobante')->find($id);
        if ($comprobante->getActivo() > 0)
            $comprobante->setActivo(0);
        else
            $comprobante->setActivo(1);  

        $comprobante->setUpdatedBy($this->getUser()->getId()); 
        $comprobante->setUpdatedAt(new \DateTime("now")); 
        
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
        $em = $this->getDoctrine()->getManager();
        $afip = $this->get('AfipFE');

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        //Lo siguiente debería modificarse para que no quede hardcodeado
        //dump($afip->getWS()->ElectronicBilling->GetVoucherTypes());
        //die;
        switch ($comprobante->getTipo()) {
            case 'FACTURA A':
                $comprobanteTipo = 1;
                break;

            case 'NOTA DEBITO A':
                $comprobanteTipo = 2;
                break;
            
            case 'NOTA CREDITO A':
                $comprobanteTipo = 3;
                break;
            
            case 'FACTURA B':
                $comprobanteTipo = 6;
                break;

            case 'NOTA DEBITO B':
                $comprobanteTipo = 7;
                break;
            
            case 'NOTA CREDITO B':
                $comprobanteTipo = 8;
                break;
            
            default:
                echo('ComprobanteTipo desconocido.');
                die;
                break;
        }

        // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
        $concepto = 1;

        //dump($afip->getWS()->ElectronicBilling->GetDocumentTypes());
        $cliente = $comprobante->getCliente();
        switch ($cliente->getDocumentoTipo()) {
            case 'CUIT':
                $clienteDocumentoTipo = 80;
                break;

            case 'CUIL':
                $clienteDocumentoTipo = 86;
                break;

            case 'DNI':
                $clienteDocumentoTipo = 96;
                break;
            
            default:
                echo('Cliente->DocumentoTipo desconocido.');
                die;
                break;
        }

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

        $alicuotasIva = $em->getRepository('AppBundle:AfipAlicuota')->findAll();

        foreach($alicuotasIva as $alicuotaIva) {
            $alicuota['Id'] = $alicuotaIva->getId(); // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
            $alicuota['BaseImp'] = 0; // Base imponible
            $alicuota['Importe'] = 0; // Importe 
            $alicuotas_all[$alicuotaIva->getId()] = $alicuota;
        }

        foreach ($comprobanteDetalles as $cd) {
            $alicuota_id = $cd->getArticulo()->getAfipAlicuota()->getId();

            $alicuotas_all[$alicuota_id]['BaseImp'] += $cd->getTotalNeto();
            $alicuotas_all[$alicuota_id]['Importe'] += $cd->getImporteIva(); 
        }

        foreach ($alicuotas_all as $alicuota) {
            if ($alicuota['Importe'] > 0) {
                $alicuotas[] = $alicuota;
            }
        }

        $data = array(
                'CantReg'   => 1,  // Cantidad de comprobantes a registrar
                'PtoVta'    => 1,  // Punto de venta
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

        $res = $afip->getWS()->ElectronicBilling->CreateNextVoucher($data);
        /*
        $res['CAE']; //CAE asignado el comprobante
        $res['CAEFchVto']; //Fecha de vencimiento del CAE (yyyy-mm-dd)
        $res['voucher_number']; //Número asignado al comprobante
        */

        $comprobante->setCaeNumero($res['CAE']);
        $comprobante->setCaeFechaVencimiento(new \DateTime($res['CAEFchVto']));
        $comprobante->setNumero($res['voucher_number']);

        $em->flush();

        dump($res);
        die;
    }

    /**
     * Imprime la factura electrónica generada por medio de WS con afip.
     *
     */
    public function facturaImprimirAction(Request $request, Comprobante $comprobante)
    {
        // You can send the html as you want
        /*
        $html = '
        <table border="1">
            <tr>
                <td>'.$comprobante->getTipo().'</td>
                <td>'.$comprobante->getPuntoVenta().'</td>
                <td>'.$comprobante->getNumero().'</td>
            </tr>
        </table>';
        */

        $em = $this->getDoctrine()->getManager();

        $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));


        $facturaTemplate = 'comprobanteventa/factura_imprimir_';



        switch ($comprobante->getTipo()) {
            case "FACTURA A":
                $facturaTemplate = 'comprobanteventa/factura_imprimir_'.'A'.'.html.twig';
                break;
            case "FACTURA B":
                $facturaTemplate = 'comprobanteventa/factura_imprimir_'.'B'.'.html.twig';
                break; 
            case "FACTURA C": 
                $facturaTemplate = 'comprobanteventa/factura_imprimir_'.'C'.'.html.twig';
                break; 
            default:
                $facturaTemplate = 'comprobanteventa/factura_imprimir_'.'B'.'.html.twig';
                break; 
        }       

        //dump($comprobanteDetalles);
        //die;

        $html = $this->renderView($facturaTemplate, array(
            'comprobante' => $comprobante,
            'comprobanteDetalles' => $comprobanteDetalles,
            'facturaTipo' => substr($comprobante->getTipo(),8,1),
            'empresa' => $this->container->getParameter('empresa'),
            'empresaRazonSocial' => $this->container->getParameter('empresa_razon_social'),
            'empresaDireccion' => $this->container->getParameter('empresa_direccion'),
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
}
