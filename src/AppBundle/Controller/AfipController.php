<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AfipController extends Controller
{
	public function testAction()
    {
    	$afip = $this->get('AfipFE');

    	//dump($afip->getWS()->RegisterScopeTen->GetTaxpayerDetails(20313311938));
    	//die;

    	dump($afip->getWS()->ElectronicBilling->GetVoucherTypes());
    	die;
    	
    	dump($afip->getWS()->ElectronicBilling->GetLastVoucher(1,6)); //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 6 (Factura B)
    	die;


    	$voucher_info = $afip->getWS()->ElectronicBilling->GetVoucherInfo(1,1,11); //Devuelve la información del comprobante 1 para el punto de venta 1 y el tipo de comprobante 6 (Factura B)

		if($voucher_info === NULL){
		    echo 'El comprobante no existe';
		}
		else{
		    echo 'Esta es la información del comprobante:';
		    echo '<pre>';
		    print_r($voucher_info);
		    echo '</pre>';
		}
		die;


    	$data = array(
				'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
				'PtoVta' 	=> 1,  // Punto de venta
				'CbteTipo' 	=> 6,  // Tipo de comprobante (ver tipos disponibles) 
				'Concepto' 	=> 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
				'DocTipo' 	=> 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
				'DocNro' 	=> 0,  // Número de documento del comprador (0 consumidor final)
				'CbteDesde' 	=> 1,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
				'CbteHasta' 	=> 1,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
				'CbteFch' 	=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
				'ImpTotal' 	=> 121, // Importe total del comprobante
				'ImpTotConc' 	=> 0,   // Importe neto no gravado
				'ImpNeto' 	=> 100, // Importe neto gravado
				'ImpOpEx' 	=> 0,   // Importe exento de IVA
				'ImpIVA' 	=> 21,  //Importe total de IVA
				'ImpTrib' 	=> 0,   //Importe total de tributos
				'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
				'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)  
				'Iva' 		=> array( // (Opcional) Alícuotas asociadas al comprobante
					array(
						'Id' 		=> 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
						'BaseImp' 	=> 100, // Base imponible
						'Importe' 	=> 21 // Importe 
					)
				), 
			);

		$res = $afip->getWS()->ElectronicBilling->CreateNextVoucher($data);

		/*
		$res['CAE']; //CAE asignado el comprobante
		$res['CAEFchVto']; //Fecha de vencimiento del CAE (yyyy-mm-dd)
		$res['voucher_number']; //Número asignado al comprobante
		*/

        dump($res);
        
        die;
    }

    public function findPersonaAction(Request $req) {
    	$normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, array('json' => new JsonEncoder()));

    	$afip = $this->get('AfipFE');

    	$persona['apellido'] = '';
    	$persona['nombre'] = '';
    	$persona['domicilio'] = '';
    	$persona['localidad_id'] = 0;
    	$persona['documento_tipo_id'] = 0;

    	$p = $afip->getWS()->RegisterScopeTen->GetTaxpayerDetails($req->get('cuit'));
    	if (is_null($p)) {
    		$existe = false;
    	}
    	else {
    		$p = (array) $p;
    		$d = (array) $p['domicilio'][0];

    		$em = $this->getDoctrine()->getManager();

    		$localidad = $em->getRepository('AppBundle:Localidad')->findOneBy(Array('codigoPostal' => $d['codPostal'], 'nombre' => $d['localidad']));

    		$documento_tipo = $em->getRepository('AppBundle:AfipDocumentoTipo')->findOneBy(Array('descripcion' => $p['tipoClave']));

    		$persona['apellido'] = $p['apellido'];
	    	$persona['nombre'] = $p['nombre'];
	    	$persona['domicilio'] = $d['direccion'];
	    	if (!is_null($localidad)) {
	    		$persona['localidad_id'] = $localidad->getId();
	    	}
	    	if (!is_null($documento_tipo)) {
	    		$persona['documento_tipo_id'] = $documento_tipo->getId();
	    	}

	    	$existe = true;
    	}

    	$j_persona = $serializer->serialize($persona, 'json');

        return JsonResponse::create(array('persona' => $j_persona, 'existe' => $existe, 'cuit' => $req->get('cuit')));
    }

}
