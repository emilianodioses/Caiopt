<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PuntoVenta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;

class InformeController extends Controller
{
    public static function mb_str_pad($texto, $longitud, $relleno = ' ', $tipo_pad = STR_PAD_RIGHT, $codificacion = null)
    {
        $diff = empty($codificacion) ? (strlen($texto) - mb_strlen($texto)) : (strlen($texto) - mb_strlen($texto, $codificacion));
        return str_pad($texto, ($longitud + $diff), $relleno, $tipo_pad);
    }

    public function afipventasalicuotasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $puntos_venta = $em->getRepository('AppBundle:PuntoVenta')->findBy(array('activo' => true),array('numero' => 'ASC'));
        $todosPts = new PuntoVenta();
        $todosPts->setNumero("Todos");
        array_unshift($puntos_venta,$todosPts);

    	return $this->render('informe/afipventasalicuotas.html.twig', array(
                'puntos_venta' => $puntos_venta));
        //return $this->render('AppBundle:Informe:afipventasalicuotas.html.twig', array());
    }

    public function afipventasalicuotasexportarAction(Request $request)
    {
        $tipo = $request->get('submit');
        $fecha_desde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fecha_hasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $punto_venta = $request->get('punto_venta');
        $punto_venta = $punto_venta == "Todos"? null : $punto_venta;

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
                $fileContent .= mb_substr(InformeController::mb_str_pad(strtoupper($comprobante->getClienteRazonSocial()), 30), 0, 30); //Apellido y nombre o denominación del comprador
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

                $fileContent .= "\r";
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

                        $fileContent .= "\r";
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

    public function ivadebitocreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        return $this->render('informe/ivaDebitoCredito.html.twig', array(
            'ivaDebitoCreditoXMes' => $this->fnIvaDebitoCreditoXMesChart($request),
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'sucursal_id' => $sucursalId,
            'usuarios' => $usuarios,
            'sucursales' => $sucursales,
            //'texto' => $texto
        ));
    }

    private function fnIvaDebitoCreditoXMesChart(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        $query = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->select('MONTH(comprobante.fecha) as mes, YEAR(comprobante.fecha) as anio, comprobante.movimiento as movimiento, SUM(comprobante.importeIva) as sumaTotal, tipo.letra as comprobanteLetra')
        ->join('comprobante.tipo','tipo')
        ->where('comprobante.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->andWhere('tipo.descripcion != \'REMITO\'')
        ->groupBy('anio')
        ->addGroupBy('mes')
        ->addGroupBy('movimiento')
        ->orderBy('anio', 'ASC')
        ->addOrderBy('mes', 'ASC')
        //->orderBy('sumaTotal', 'DESC')
        //->setMaxResults(10)
        //->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $query_datos = array();
        //Inicializo el array en cero
        foreach($query as $key => $xx) {
            $query_datos[$xx["mes"].'-'.$xx["anio"]]["mes"] = $xx["mes"].'-'.$xx["anio"];
            $query_datos[$xx["mes"].'-'.$xx["anio"]]["iva_credito"] = 0;
            $query_datos[$xx["mes"].'-'.$xx["anio"]]["iva_debito"] = 0;
        }

        foreach($query as $key => $xx) {
            if ($xx["movimiento"] == "Compra" && $xx['comprobanteLetra'] == 'A'){
                $query_datos[$xx["mes"].'-'.$xx["anio"]]["iva_credito"] += $xx["sumaTotal"] ;
            }
            elseif ($xx["movimiento"] == "Venta") {
                $query_datos[$xx["mes"].'-'.$xx["anio"]]["iva_debito"] += $xx["sumaTotal"] ;
            }
        }

        $data = array();
        $header = array('Mes', 'IVA Crédito', 'IVA Débito');
        array_push($data, $header);

        foreach($query_datos as $mes) {
            $item = array($mes["mes"], (int)$mes["iva_credito"], (float)$mes["iva_debito"]);
            array_push($data, $item);
        }
        //dump($query);
        //die;

        $chart = new ColumnChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('IVA Débito/Crédito por Mes');
        $chart->getOptions()->setHeight(400);
        //$chart->getOptions()->setWidth(300);
        $chart->getOptions()->getHAxis()->setTitle('Mes');
        $chart->getOptions()->getHAxis()->setSlantedText(true);
        $chart->getOptions()->getHAxis()->setSlantedTextAngle('90');
        $chart->getOptions()->getHAxis()->getTextStyle()->setFontSize(10);
        //$chart->getOptions()->getChartArea()->setHeight('40%');
        //$chart->getOptions()->getChartArea()->setTop('70');
        //$chart->getOptions()->getLegend()->setPosition('left');
        //$chart->getOptions()->getLegend()->setAlignment('vertical');
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $chart;
    // Google Charts
    }

    public function gastosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        return $this->render('informe/gastos.html.twig', array(
            'gastosXCategoria' => $this->fnGastosXCategoriaChart($request),
            'gastosXSucursal' => $this->fnGastosXSucursalChart($request),
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'sucursal_id' => $sucursalId,
            'usuarios' => $usuarios,
            'sucursales' => $sucursales,
            //'texto' => $texto
        ));
    }

    private function fnGastosXCategoriaChart(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $query = $em->getRepository('AppBundle:LibroCajaDetalle')
        ->createQueryBuilder('lcd')
        ->join('lcd.libroCaja','lc')
        ->join('lcd.movimientoCategoria','mc')
        ->select('mc.nombre as categoriaNombre, SUM(lcd.importe) as sumaTotal')
        ->where('lcd.tipo = :tipo')
        ->andWhere('lcd.activo = 1')
        ->andWhere('lc.fecha >= :fechaDesde')
        ->andWhere('lc.fecha <= :fechaHasta')
        ->groupBy('categoriaNombre')
        ->orderBy('sumaTotal', 'DESC')
        //->setMaxResults(10)
        ->setParameter('tipo', "Egreso de Caja")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('lc.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Categoría', 'Suma Total');
        array_push($data, $header);

        foreach($query as $xx) {
            $item = array($xx["categoriaNombre"], (int)$xx["sumaTotal"]);
            array_push($data, $item);
        }

        $chart = new PieChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('Gastos por Categoría en $');
        $chart->getOptions()->setHeight(400);
        //$chart->getOptions()->setWidth(300);
        $chart->getOptions()->getTitleTextStyle()->setBold(true);
        $chart->getOptions()->setis3D(true);
        $chart->getOptions()->getLegend()->setPosition('left');
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $chart;
    // Google Charts
    }

    private function fnGastosXSucursalChart(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $query = $em->getRepository('AppBundle:LibroCajaDetalle')
        ->createQueryBuilder('lcd')
        ->join('lcd.libroCaja','lc')
        ->join('lc.sucursal','s')
        ->select('s.nombre as sucursalNombre, SUM(lcd.importe) as sumaTotal')
        ->where('lcd.tipo = :tipo')
        ->andWhere('lcd.activo = 1')
        ->andWhere('lc.fecha >= :fechaDesde')
        ->andWhere('lc.fecha <= :fechaHasta')
        ->groupBy('lc.sucursal')
        ->orderBy('sumaTotal', 'DESC')
        //->setMaxResults(10)
        ->setParameter('tipo', "Egreso de Caja")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('lc.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Sucursal', 'Suma Total');
        array_push($data, $header);

        foreach($query as $xx) {
            $item = array($xx["sucursalNombre"], (int)$xx["sumaTotal"]);
            array_push($data, $item);
        }

        $chart = new PieChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('Gastos por Sucursal en $');
        $chart->getOptions()->setHeight(400);
        //$chart->getOptions()->setWidth(300);
        $chart->getOptions()->getTitleTextStyle()->setBold(true);
        $chart->getOptions()->setis3D(true);
        $chart->getOptions()->getLegend()->setPosition('left');
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $chart;
    // Google Charts
    }

    public function recibosVentasMedicosAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $cliente = $request->get('cliente','');
        $medico = $request->get('medico','');

        $query = $em->getRepository('AppBundle:ReciboComprobante')
        ->createQueryBuilder('rc')
        ->join('rc.recibo','r')
        ->select('c as comprobante, SUM(rc.importe) as sumaTotal')
        ->from('AppBundle:Comprobante','c')
        ->where('rc.activo = 1')
        ->andWhere('r.activo = 1')
        ->andWhere('c.activo = 1')
        ->andWhere('r.fecha >= :fechaDesde')
        ->andWhere('r.fecha <= :fechaHasta')
        ->andWhere('rc.comprobante = c.id')
        ->andWhere('c.medico > 0')
        ->groupBy('c.id')
        //->orderBy('sumaTotal', 'DESC')
        //->setMaxResults(10)
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($cliente != '') {
            $query = $query
                ->join('c.cliente', 'cli')
                ->andWhere('cli.nombre LIKE :cliente OR cli.documentoNumero LIKE :cliente')
                ->setParameter('cliente', '%'.$cliente.'%');
        }

        if ($medico != '') {
            $query = $query
                ->join('c.medico', 'med')
                ->andWhere('med.nombre LIKE :medico OR med.matricula LIKE :medico')
                ->setParameter('medico', '%'.$medico.'%');
        }

        $recibosComprobantes = $query->getQuery()
            ->getResult();

        //dump($recibosComprobantes);
        //die;
        return $this->render('informe/recibosVentasMedicos.html.twig', array(
            'recibosComprobantes' => $recibosComprobantes,
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'cliente' => $cliente,
            'medico' => $medico,
        ));
    }

    /**
    *Reporte Ventas Médicos %
    */
    public function recibosVentasMedicos_imprimirAction(Request $request){


      $em = $this->getDoctrine()->getManager();

      $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
      $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
      $cliente = $request->get('cliente');
      $medico = $request->get('medico');

      $query = $em->getRepository('AppBundle:ReciboComprobante')
      ->createQueryBuilder('rc')
      ->join('rc.recibo','r')
      ->select('c as comprobante, SUM(rc.importe) as sumaTotal')
      ->from('AppBundle:Comprobante','c')
      ->where('rc.activo = 1')
      ->andWhere('r.activo = 1')
      ->andWhere('c.activo = 1')
      ->andWhere('r.fecha >= :fechaDesde')
      ->andWhere('r.fecha <= :fechaHasta')
      ->andWhere('rc.comprobante = c.id')
      ->andWhere('c.medico > 0')
      ->groupBy('c.id')
      //->orderBy('sumaTotal', 'DESC')
      //->setMaxResults(10)
      ->setParameter('fechaDesde', $fechaDesde)
      ->setParameter('fechaHasta', $fechaHasta);

      if ($cliente != '') {
          $query = $query
              ->join('c.cliente', 'cli')
              ->andWhere('cli.nombre LIKE :cliente OR cli.documentoNumero LIKE :cliente')
              ->setParameter('cliente', '%'.$cliente.'%');
      }

      if ($medico != '') {
          $query = $query
              ->join('c.medico', 'med')
              ->andWhere('med.nombre LIKE :medico OR med.matricula LIKE :medico')
              ->setParameter('medico', '%'.$medico.'%');
      }

      $recibosComprobantes = $query->getQuery()
          ->getResult();

      //dump($recibosComprobantes);
      //die;

      $ventasTemplate = 'informe/recibosVentasMedicos_imprimir.html.twig';
      $html = $this->renderView($ventasTemplate, array(
          'recibosComprobantes' => $recibosComprobantes,
          'fecha_desde' => $fechaDesde->format('d-m-Y'),
          'fecha_hasta' => $fechaHasta->format('d-m-Y'),
          'cliente' => $cliente,
          'medico' => $medico,
      ));

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

      $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
      $filename = "informe_venta_medicos";
      //$pdf->Output($filename,'I'); // This will output the PDF as a response directly
      $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a file

      return true;

    }
}
