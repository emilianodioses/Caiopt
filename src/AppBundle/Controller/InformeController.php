<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class InformeController extends Controller
{
    public function afipventasalicuotasAction(Request $request)
    {
    	return $this->render('informe/afipventasalicuotas.html.twig', array());
        //return $this->render('AppBundle:Informe:afipventasalicuotas.html.twig', array());
    }

    public function afipventasalicuotasexportarAction(Request $request)
    {
        $tipo = $request->get('submit');
        $fecha_desde = new \DateTime($request->get('fecha_desde'));
        $fecha_hasta = new \DateTime($request->get('fecha_hasta'));
        $punto_venta = $request->get('punto_venta');

        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy_ventasFacturadasGeneral($fecha_desde, $fecha_hasta, $punto_venta);
        
        if ($tipo == 'ventas') {
            // Provide a name for your file with extension
            $filename = 'VENTAS_'.sprintf("%05d", $punto_venta).'_'.$fecha_desde->format('Ymd').'-'.$fecha_hasta->format('Ymd').'.txt';
            
            // The dinamically created content of the file
            $fileContent = "";
            foreach ($comprobantes as $key => $comprobante) {
                $documento_tipo = $em->getRepository('AppBundle:AfipDocumentoTipo')->findOneBy(array('descripcion' => $comprobante->getClienteDocumentoTipo()));

                $alicuotas_cantidad = $em->getRepository('AppBundle:Comprobante')->count_alicuotas($comprobante);

                $fileContent .= $comprobante->getFecha()->format('Ymd'); //Fecha de comprobante
                $fileContent .= sprintf("%03d", $comprobante->getTipo()->getCodigo()); //Tipo de comprobante
                $fileContent .= sprintf("%05d", $comprobante->getPuntoVenta()); //Punto de venta
                $fileContent .= sprintf("%020d", $comprobante->getAfipNumero()); //Número de comprobante
                $fileContent .= sprintf("%020d", $comprobante->getAfipNumero()); //Número de comprobante hasta
                $fileContent .= sprintf("%02d", $documento_tipo->getCodigo()); //Código de documento del comprador
                $fileContent .= sprintf("%020d", $comprobante->getClienteDocumentoNumero()); //Número de identificación del comprador
                $fileContent .= substr(str_pad(strtoupper($comprobante->getClienteRazonSocial()), 30), 0, 30); //Apellido y nombre o denominación del comprador
                $fileContent .= sprintf("%015d", $comprobante->getTotal()*100); //Importe total de la operación
                $fileContent .= sprintf("%015d", 0); //Importe total de conceptos que no integran el precio neto gravado
                $fileContent .= sprintf("%015d", 0); //Percepción a no categorizados
                $fileContent .= sprintf("%015d", 0); //Importe de operaciones exentas
                $fileContent .= sprintf("%015d", 0); //Importe de percepciones o pagos a cuenta de impuestos Nacionales
                $fileContent .= sprintf("%015d", 0); //Importe de percepciones de Ingresos Brutos
                $fileContent .= sprintf("%015d", 0); //Importe de percepciones impuestos Municipales
                $fileContent .= sprintf("%015d", 0); //Importe impuestos internos
                $fileContent .= 'PES'; //Código de moneda
                $fileContent .= '0001000000'; //Tipo de cambio
                $fileContent .= $alicuotas_cantidad; //Cantidad de alícuotas de IVA
                $fileContent .= '0'; //Código de operación
                $fileContent .= sprintf("%015d", 0); //Otros Tributos
                $fileContent .= sprintf("%08d", 0); //Fecha de Vencimiento de Pago

                $fileContent .= "\n";
            }
        }
        elseif ($tipo == 'alicuotas') {
            // Provide a name for your file with extension
            $filename = 'ALICUOTAS_'.sprintf("%05d", $punto_venta).'_'.$fecha_desde->format('Ymd').'-'.$fecha_hasta->format('Ymd').'.txt';

            $alicuotasIva = $em->getRepository('AppBundle:AfipAlicuota')->findBy(Array('activo'=>1));

            // The dinamically created content of the file
            $fileContent = "";
            foreach ($comprobantes as $key => $comprobante) {
                foreach($alicuotasIva as $alicuotaIva) {
                    $alicuotas[$alicuotaIva->getId()]['Codigo'] = $alicuotaIva->getCodigo(); // Id del tipo de IVA (5 para 21%)(ver tipos disponibles)
                    $alicuotas[$alicuotaIva->getId()]['BaseImp'] = 0; // Base imponible
                    $alicuotas[$alicuotaIva->getId()]['Importe'] = 0; // Importe 
                }

                $comprobanteDetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo'=>1));

                foreach ($comprobanteDetalles as $cd) {
                    $alicuota_id = $em->getRepository('AppBundle:AfipAlicuota')->findOneBy(array('activo'=>1, 'descripcion' => $cd->getPorcentajeIva()))->getId();

                    $alicuotas[$alicuota_id]['BaseImp'] += $cd->getTotalNeto();
                    $alicuotas[$alicuota_id]['Importe'] += $cd->getImporteIva(); 
                }

                foreach($alicuotasIva as $alicuotaIva) {
                    if ($alicuotas[$alicuotaIva->getId()]['BaseImp'] > 0) {
                        $fileContent .= sprintf("%03d", $comprobante->getTipo()->getCodigo()); //Tipo de comprobante
                        $fileContent .= sprintf("%05d", $comprobante->getPuntoVenta()); //Punto de venta
                        $fileContent .= sprintf("%020d", $comprobante->getAfipNumero()); //Número de comprobante
                        $fileContent .= sprintf("%015d", $alicuotas[$alicuotaIva->getId()]['BaseImp']*100); //Importe neto gravado
                        $fileContent .= sprintf("%04d", $alicuotas[$alicuotaIva->getId()]['Codigo']); //Alícuota de IVA
                        $fileContent .= sprintf("%015d", $alicuotas[$alicuota_id]['Importe']*100); //Impuesto Liquidado
                        
                        $fileContent .= "\n";
                    }
                }
            }
        }
        else {
            echo 'Error - Tipo desconocido';
            die;
        }
        
        // Return a response with a specific content
        $response = new Response($fileContent);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }
}
