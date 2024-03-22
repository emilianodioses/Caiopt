<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajoContactologiaDetalle;
use AppBundle\Entity\PuntoVenta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Comprobante;
use AppBundle\Entity\OrdenTrabajo;
use AppBundle\Entity\OrdenTrabajoDetalle;
use AppBundle\Entity\OrdenTrabajoContactologia;
use AppBundle\Entity\PagoTipo;
use AppBundle\Entity\ReciboComprobante;
use AppBundle\Entity\ClientePago;
use AppBundle\Entity\Recibo;
use AppBundle\Entity\Sucursal;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\GroupBy;

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
                        $fileContent .= sprintf("%015d", $alicuotas[$alicuotaIva->getId()]['Importe']*100); //Impuesto Liquidado
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

    /**
     *Reporte Libro Iva Ventas
     */
    public function libroIvaVentasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");


        $query = $em->getRepository('AppBundle:Comprobante')
            ->createQueryBuilder('comprobante')
            ->join('comprobante.tipo','tipo')
            ->join('comprobante.cliente','cliente')
            ->select('comprobante.fecha','tipo.descripcion','tipo.codigo','CONCAT(LPAD(comprobante.puntoVenta,5,0),\' - \',LPAD(comprobante.numero,8,0)) as nro_comprobante',
                'comprobante.clienteRazonSocial','comprobante.totalNeto','cliente.documentoNumero',
                'comprobante.totalNoGravado','comprobante.importeIva','comprobante.importeIvaExento',
		    'comprobante.total')
            ->andWhere('tipo.codigo = 1 OR tipo.codigo = 6') // Factura A y Factura B: TODO: Refactorizar a constante
            ->andWhere('comprobante.fecha >= :fechaDesde')
            ->andWhere('comprobante.fecha <= :fechaHasta')
            ->andWhere('comprobante.caeNumero IS NOT NULL')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

             $informeIvaVentas = $query->getQuery()
            ->getResult();


        return $this->render('informe/libroIvaVentas.html.twig', array(
            'informeIvaVentas' => $informeIvaVentas,
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),

        ));
    }

    public function libroIvaVentas_imprimirAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");

        $query = $em->getRepository('AppBundle:Comprobante')
            ->createQueryBuilder('comprobante')
            ->join('comprobante.tipo','tipo')
            ->join('comprobante.cliente','cliente')
            ->select('comprobante.fecha','tipo.descripcion','tipo.codigo','CONCAT(LPAD(comprobante.puntoVenta,5,0),\' - \',LPAD(comprobante.numero,8,0)) as nro_comprobante',
                'comprobante.clienteRazonSocial','comprobante.totalNeto','cliente.documentoNumero',
                'comprobante.totalNoGravado','comprobante.importeIva','comprobante.importeIvaExento',
                'comprobante.total')
            ->andWhere('tipo.codigo = 1 OR tipo.codigo = 6') // Factura A y Factura B: TODO: Refactorizar a constante
            ->andWhere('comprobante.fecha >= :fechaDesde')
            ->andWhere('comprobante.fecha <= :fechaHasta')
            ->andWhere('comprobante.caeNumero IS NOT NULL')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        $informeIvaVentas = $query->getQuery()
            ->getResult();


        $ventasTemplate = 'informe/libroIvaVentas_imprimir.html.twig';
        $html = $this->renderView($ventasTemplate, array(
            '$informeIvaVentas' => $informeIvaVentas,
            'informeIvaVentas' => $informeIvaVentas,
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
        ));

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $filename = "informe_iva_ventas";
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a file

        return true;
    }

    public function libroIvaVentas_imprimirExcelAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $totalNeto = 0;
        $NoGrav = 0;
        $DebFiscal = 0;
        $IvaDisc = 0;
        $IvaExento = 0;
        $TotalOps = 0;
        $Alic = 21.000;
        $Ret = 0;

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");


        $query = $em->getRepository('AppBundle:Comprobante')
            ->createQueryBuilder('comprobante')
            ->join('comprobante.tipo','tipo')
            ->join('comprobante.cliente','cliente')
            ->select('SUBSTRING(comprobante.fecha, 1, 10)','tipo.descripcion','CONCAT(LPAD(comprobante.puntoVenta,5,0),\' - \',LPAD(comprobante.numero,8,0)) as nro_comprobante',
                'comprobante.clienteRazonSocial','cliente.documentoNumero','comprobante.totalNeto',
                'comprobante.totalNoGravado','comprobante.importeIva','comprobante.importeIvaExento',
                'comprobante.total', 'tipo.codigo')
            ->andWhere('tipo.codigo = 1 OR tipo.codigo = 6') // Factura A y Factura B: TODO: Refactorizar a constante
            ->andWhere('comprobante.fecha >= :fechaDesde')
            ->andWhere('comprobante.fecha <= :fechaHasta')
            ->andWhere('comprobante.caeNumero IS NOT NULL')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        $informeIvaVentas = $query->getQuery()
            ->getResult();

        $f = fopen('php://memory', 'w');
        $rows = array('Fecha','Comprobante','Nro Comp.','Cliente','CUIT o Doc','Neto','Conc No Grav Ret/Perc/PCta', 'Deb Fiscal IVA Discrim.',
            'Acrecen Op Exentas', 'Total Operac');
        fputcsv($f,$rows,";");

        foreach ($informeIvaVentas as $line) {
            fputcsv($f, array_slice($line, 0, 10), ";");
            $totalNeto = $totalNeto + $line['totalNeto'];
            $NoGrav = $NoGrav + $line['totalNoGravado'];
            if ($line['codigo'] == 6)
                $DebFiscal = $DebFiscal + $line['importeIva'];
            else
                $IvaDisc = $IvaDisc + $line['importeIva'];
            $IvaExento = $IvaExento + $line['importeIvaExento'];
            $TotalOps = $TotalOps + $line['total'];
        }

        $totals = array('','','','','Totales mensuales',$totalNeto,$NoGrav, $DebFiscal, $IvaExento, $TotalOps);
        fputcsv($f,$totals,";");
        $totals2 = array('','','','','','',$Ret, $IvaDisc, $IvaExento, '');
        fputcsv($f,$totals2,";");
        $totals3 = array('Totales mensuales','Neto: '.$totalNeto ,'Debito fiscal: '.$DebFiscal,
            'Iva Acrec: '. $IvaExento,'Alic.: 21','','', '', '', '');
        fputcsv($f,$totals3,";");


        fseek($f, 0);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="LibroIvaVentas.csv";');
        fpassthru($f);
        return new Response();
    }

    // Informe de ventas en efectivo
    public function ventasEfectivoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde') . " 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta') . " 23:59:59");
        $sucursalIds = $request->get('sucursal');

        $sucursales = $em->getRepository(Sucursal::class)->findAll();

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('cli.nombre, ot.id as id_OT, c.caeNumero, c.fecha, c.total,
            (c.total - COALESCE(SUM(rc.importe), 0)) as diferencia, pt.nombre as pagoTipo, s.nombre as nombreSucursal')
            ->from(Cliente::class, 'cli')
            ->innerJoin(Comprobante::class, 'c', 'WITH', 'cli.id = c.cliente')
            ->leftJoin(OrdenTrabajo::class, 'ot', 'WITH', 'ot.id = c.ordenTrabajo')
            ->leftJoin(ReciboComprobante::class, 'rc', 'WITH', 'rc.comprobante = c.id')
            ->leftJoin(Recibo::class, 'r', 'WITH', 'r.id = rc.recibo')
            ->leftJoin(ClientePago::class, 'cp', 'WITH', 'cp.recibo = r.id')
            ->leftJoin(PagoTipo::class, 'pt', 'WITH', 'cp.pagoTipo = pt.id')
            ->leftJoin(Sucursal::class, 's', 'WITH', 's.id = c.sucursal')
            ->where('c.fecha >= :fechaDesde')
            ->andWhere('c.fecha <= :fechaHasta')
            ->andWhere('pt.id = :pagoTipoId')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta)
            ->setParameter('pagoTipoId', 1) // ID del tipo de pago para ventas en efectivo
            ->groupBy('cli.id, c.id, ot.id, pt.id')
            ->orderBy('c.fecha');

        // Agregar condición de sucursal si se seleccionan sucursales
        if (!empty($sucursalIds)) {
            $query->andWhere('c.sucursal IN (:sucursalIds)')
                ->setParameter('sucursalIds', $sucursalIds);
        }

        $ventasEfectivo = $query->getQuery()->getResult();

        // Renderizar la plantilla
        return $this->render('informe/ventasEfectivo.html.twig', array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'ventasEfectivo' => $ventasEfectivo,
            'sucursales' => $sucursales, // Pasar las sucursales disponibles a la plantilla Twig
            'sucursal_ids' => $sucursalIds, // Pasar los IDs de sucursales seleccionadas
        ));
    }

    // Informe ventas con tarjeta de crédito
    public function ventasCreditoAction(Request $request){
	 
        $em = $this->getDoctrine()->getManager();
        
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalIds = $request->get('sucursal');

        $sucursales = $em->getRepository(Sucursal::class)->findAll();

        // Creo la consulta
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('cli.nombre, ot.id as id_OT, c.caeNumero, c.fecha, c.total, pt.nombre as tipoPago,
            (c.total - COALESCE(SUM(rc.importe), 0)) as diferencia, pt.nombre as pagoTipo, s.nombre as nombreSucursal')
            ->from(Cliente::class, 'cli')
            ->innerJoin(Comprobante::class, 'c', 'WITH', 'cli.id = c.cliente')
            ->leftJoin(OrdenTrabajo::class, 'ot', 'WITH', 'ot.id = c.ordenTrabajo')
            ->leftJoin(ReciboComprobante::class, 'rc', 'WITH', 'rc.comprobante = c.id')
            ->leftJoin(Recibo::class, 'r', 'WITH', 'r.id = rc.recibo')
            ->leftJoin(ClientePago::class, 'cp', 'WITH', 'cp.recibo = r.id')
            ->leftJoin(PagoTipo::class, 'pt', 'WITH', 'pt.id = cp.pagoTipo')
            ->leftJoin(Sucursal::class, 's', 'WITH', 's.id = c.sucursal')
            // IDs de tipo de pago Tarjeta de crédito, débito y transferencia
            ->andWhere($queryBuilder->expr()->in('pt.id', [2, 3, 5])) 
            ->andWhere('c.fecha >= :fechaDesde')
            ->andWhere('c.fecha <= :fechaHasta')
            ->groupBy('cli.id, c.id, ot.id, pt.id')
            ->orderBy('c.fecha')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);
            
        // Agregar condición de sucursal si se seleccionan sucursales
        if (!empty($sucursalIds)) {
            $query->andWhere('c.sucursal IN (:sucursalIds)')
                ->setParameter('sucursalIds', $sucursalIds);
        }

        $ventasCredito = $query->getQuery()->getResult();

        // Renderizar la plantilla
        return $this->render('informe/ventasCredito.html.twig', array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'ventasCredito' => $ventasCredito,
            'sucursales' => $sucursales, // Pasar las sucursales disponibles a la plantilla Twig
            'sucursal_ids' => $sucursalIds, // Pasar los IDs de sucursales seleccionadas
        ));
    }

    // Excel Ventas en Efectivo
    public function ventasEfectivo_imprimirExcelAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde') . " 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta') . " 23:59:59");
        $sucursalIds = $request->get('sucursal');

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('cli.nombre, ot.id as id_OT, c.caeNumero, c.fecha, c.total,
            (c.total - COALESCE(SUM(rc.importe), 0)) as diferencia, pt.nombre as pagoTipo, s.nombre as nombreSucursal')
            ->from(Cliente::class, 'cli')
            ->innerJoin(Comprobante::class, 'c', 'WITH', 'cli.id = c.cliente')
            ->leftJoin(OrdenTrabajo::class, 'ot', 'WITH', 'ot.id = c.ordenTrabajo')
            ->leftJoin(ReciboComprobante::class, 'rc', 'WITH', 'rc.comprobante = c.id')
            ->leftJoin(Recibo::class, 'r', 'WITH', 'r.id = rc.recibo')
            ->leftJoin(ClientePago::class, 'cp', 'WITH', 'cp.recibo = r.id')
            ->leftJoin(PagoTipo::class, 'pt', 'WITH', 'cp.pagoTipo = pt.id')
            ->leftJoin(Sucursal::class, 's', 'WITH', 's.id = c.sucursal')
            ->andWhere('c.fecha >= :fechaDesde')
            ->andWhere('c.fecha <= :fechaHasta')
            ->andWhere('pt.id = :pagoTipoId')
            ->groupBy('cli.id, c.id, ot.id, pt.id')
            ->orderBy('c.fecha')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta)
            ->setParameter('pagoTipoId', 1); // ID del tipo de pago para ventas en efectivo

        // Agregar condición de sucursal si se seleccionan sucursales
        if (!empty($sucursalIds)) {
            $query->andWhere('c.sucursal IN (:sucursalIds)')
                ->setParameter('sucursalIds', $sucursalIds);
        }

        // Obtengo los resultados
        $ventasEfectivo = $query->getQuery()->getResult();

        // Crear archivo csv
        $file = fopen('php://memory', 'w');
        $rows = array('Cliente', 'Facturada', 'Nro Interno', 'Fecha', 'Total Venta', 'Pendiente pago', 'Sucursal');
        fputcsv($file, $rows, ";");

        foreach ($ventasEfectivo as $venta) {
            fputcsv($file, array(
                $venta['nombre'],
                isset($venta['caeNumero']) ? 'SI' : 'NO',
                $venta['id_OT'],
                $venta['fecha']->format('d-m-Y'),
                $venta['total'],
                ($venta['diferencia'] > 0) ? 'Pendiente' : 'Pagado',
                $venta['nombreSucursal']
            ), ';');
        }

        rewind($file);
        $content = stream_get_contents($file);
        fclose($file);

        // Enviar archivo CSV al navegador
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="InformeVentasConEfectivo.csv"');

        return $response;
    }

    // Imprimir tabla de ventas con tarjeta crédito en formato excel
    public function ventasCredito_imprimirExcelAction(Request $request)
    {
        $fechaDesde = new \DateTime($request->get('fecha_desde') . " 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta') . " 23:59:59");
        $sucursalIds = $request->get('sucursal');

        // Creo la consulta
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('cli.nombre, ot.id as id_OT, c.caeNumero, c.fecha, c.total,
            (c.total - COALESCE(SUM(rc.importe), 0)) as diferencia, pt.nombre as tipoPago, s.nombre as nombreSucursal')
            ->from(Cliente::class, 'cli')
            ->innerJoin(Comprobante::class, 'c', 'WITH', 'cli.id = c.cliente')
            ->leftJoin(OrdenTrabajo::class, 'ot', 'WITH', 'ot.id = c.ordenTrabajo')
            ->leftJoin(ReciboComprobante::class, 'rc', 'WITH', 'rc.comprobante = c.id')
            ->leftJoin(Recibo::class, 'r', 'WITH', 'r.id = rc.recibo')
            ->leftJoin(ClientePago::class, 'cp', 'WITH', 'cp.recibo = r.id')
            ->leftJoin(PagoTipo::class, 'pt', 'WITH', 'pt.id = cp.pagoTipo')
            ->leftJoin(Sucursal::class, 's', 'WITH', 's.id = c.sucursal')
            // IDs de tipo de pago Tarjeta de crédito, débito y transferencia
            ->andWhere($queryBuilder->expr()->in('pt.id', [2, 3, 5])) 
            ->andWhere('c.fecha >= :fechaDesde')
            ->andWhere('c.fecha <= :fechaHasta')
            ->groupBy('cli.id, c.id, ot.id, pt.id')
            ->orderBy('c.fecha')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        // Agregar condición de sucursal si se seleccionan sucursales
        if (!empty($sucursalIds)) {
            $query->andWhere('c.sucursal IN (:sucursalIds)')
                ->setParameter('sucursalIds', $sucursalIds);
        }

        // Obtengo los resultados
        $ventasCredito = $query->getQuery()->getResult();

        // Crear archivo csv
        $file = fopen('php://memory', 'w');
        $rows = array('Cliente', 'Facturada', 'Nro Interno', 'Fecha', 'Total Venta', 'Forma de pago', 'Pendiente pago', 'Sucursal');
        fputcsv($file, $rows, ";");

        foreach ($ventasCredito as $venta) {
            fputcsv($file, array(
                $venta['nombre'],
                isset($venta['caeNumero']) ? 'SI' : 'NO',
                $venta['id_OT'],
                $venta['fecha']->format('d-m-Y'),
                $venta['total'],
                $venta['tipoPago'],
                ($venta['diferencia'] > 0) ? 'Pendiente ( ' . $venta['diferencia'] . ' )' : 'Pagado',
                $venta['nombreSucursal']
            ), ';');
        }

        rewind($file);
        $content = stream_get_contents($file);
        fclose($file);

         // Enviar archivo CSV al navegador
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="InformeVentasConCredito.csv"');

        return $response;

    }

    // Informe Colegio de Ópticos OT
    public function colegioOpticoOTAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde') . " 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta') . " 23:59:59");
        $sucursalId = $request->get('sucursal');

        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('ot.id as id_OT, ot.id as id_OrdenTrabajo, cli.nombre as nombreCliente, cli.direccion, 
        ot.fechaReceta, med.nombre as medicoNombre')
            ->from(OrdenTrabajo::class, 'ot')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = ot.cliente')
            ->leftJoin('ot.medico', 'med')
            ->where('ot.fechaReceta >= :fechaDesde')
            ->andWhere('ot.fechaReceta <= :fechaHasta')
            ->orderBy('ot.fechaReceta')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query
                ->leftJoin('ot.sucursal', 'sucursal')
                ->andWhere('sucursal.id = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $informeOTs = $query->getQuery()->getResult();

        return $this->render('informe/colegioOpticoOT.html.twig', array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'informeOTs' => $informeOTs,
            'sucursal_id' => $sucursalId,
            'sucursales' => $sucursales,
        ));
    }


    /**
     * Reporte Colegio de Ópticos OT
     */
    public function colegioOpticoOT_imprimirAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('ot.id as id_OT, ot.id as id_OrdenTrabajo, cli.nombre as nombreCliente, cli.direccion, 
            ot.fechaReceta, med.nombre as medicoNombre, med.matricula as medicoMatricula,
            COUNT(otd.id) as cantidadCristales, otd.tipoCristal, ot.fechaRecepcion, ot.fechaEntrega')
            ->from(OrdenTrabajo::class, 'ot')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = ot.cliente')
            ->leftJoin('ot.medico', 'med')
            ->leftJoin(OrdenTrabajoDetalle::class, 'otd', 'WITH', 'otd.ordenTrabajo = ot.id')
            ->where('ot.fechaReceta >= :fechaDesde')
            ->andWhere('ot.fechaReceta <= :fechaHasta')
            ->orderBy('ot.fechaReceta')
            ->groupBy('ot.id, otd.id, otd.tipoCristal')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        $informeOTs = $query->getQuery()->getResult();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder2 = $em->createQueryBuilder();
        $query2 = $queryBuilder2
            ->select('ot.id, ot.fechaReceta, cli.nombre as nombreCliente, cli.direccion, med.nombre as medicoNombre,
            med.matricula as medicoMatricula, ot.lejosOjoDerechoEsfera, ot.lejosOjoDerechoCilindro, ot.lejosOjoDerechoEje,
            ot.lejosOjoIzquierdoEsfera, ot.lejosOjoIzquierdoCilindro, ot.lejosOjoIzquierdoEje,
            ot.cercaOjoDerechoEsfera, ot.cercaOjoDerechoCilindro, ot.cercaOjoDerechoEje,
            ot.cercaOjoIzquierdoEsfera, ot.cercaOjoIzquierdoCilindro, ot.cercaOjoIzquierdoEje,
            ot.fechaRecepcion, ot.fechaEntrega, GROUP_CONCAT(marco.descripcion SEPARATOR \', \') as descripcionMarcos')
            ->from(OrdenTrabajo::class, 'ot')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = ot.cliente')
            ->leftJoin('ot.medico', 'med')
            ->leftJoin(OrdenTrabajoDetalle::class, 'otd', 'WITH', 'otd.ordenTrabajo = ot.id')
            ->leftJoin('otd.articulo', 'a') // Relacionar con la tabla de artículo
            ->leftJoin('a.marco', 'marco')
            ->where('ot.fechaReceta >= :fechaDesde')
            ->andWhere('ot.fechaReceta <= :fechaHasta')
            ->orderBy('ot.fechaReceta')
            ->groupBy('ot.id, ot.fechaReceta, cli.nombre, cli.direccion, med.nombre, med.matricula, ot.lejosOjoDerechoEsfera, ot.lejosOjoDerechoCilindro, ot.lejosOjoDerechoEje, ot.lejosOjoIzquierdoEsfera, ot.lejosOjoIzquierdoCilindro, ot.lejosOjoIzquierdoEje, ot.cercaOjoDerechoEsfera, ot.cercaOjoDerechoCilindro, ot.cercaOjoDerechoEje, ot.cercaOjoIzquierdoEsfera, ot.cercaOjoIzquierdoCilindro, ot.cercaOjoIzquierdoEje, ot.fechaRecepcion, ot.fechaEntrega')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query2
                ->leftJoin('ot.sucursal', 'sucursal')
                ->andWhere('sucursal.id = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
            $sucursal = $em->getRepository(Sucursal::class)->find($sucursalId);
        }

        $ordenes_trabajo = $query2->getQuery()->getResult();

        $idAnterior = null;
        $cantidadLejos = 0;
        $cantidadCerca = 0;
        $cantidadBifocal = 0;
        $cantidadOcupacional = 0;
        $cantidadProgresivo = 0;
        $arregloCristales = array();

        // Recorro los resultados de la consulta para obtener las cantidades de los
        // diferentes tipos de cristales que correspondan a la misma OT
        foreach ($informeOTs as $informeOT ){
            $idActual = $informeOT['id_OT'];
            $tipoCristal = $informeOT['tipoCristal'];
            if ($idActual == $idAnterior){
                switch($tipoCristal){
                    case 'Lejos':
                        $cantidadLejos = $informeOT['cantidadCristales'];
                        break;
                    case 'Cerca':
                        $cantidadCerca = $informeOT['cantidadCristales'];
                        break;
                    case 'Bifocal':
                        $cantidadBifocal = $informeOT['cantidadCristales'];
                        break;
                    case 'Ocupacional':
                        $cantidadOcupacional = $informeOT['cantidadCristales'];
                        break;
                    case 'Progresivo':
                        $cantidadProgresivo = $informeOT['cantidadCristales'];
                        break;
                    default:
                        break;
                }
            }
            else{
                // Armo string
                $cristales = "Lejos ($cantidadLejos), Cerca ($cantidadCerca), 
                    Bifocal ($cantidadBifocal), Ocupacional ($cantidadOcupacional),
                    Progresivo ($cantidadProgresivo)";

                // Agrego el string al arreglo
                $arregloCristales[$idAnterior] = $cristales;

                // Reinicio contadores
                $idAnterior = $idActual;
                $cantidadLejos = 0;
                $cantidadCerca = 0;
                $cantidadBifocal = 0;
                $cantidadOcupacional = 0;
                $cantidadProgresivo = 0;

                // Asignar cantidad del primer informe que no pasa por true
                // ya que la variable IdAnterior se inicializa en null
                switch($tipoCristal){
                    case 'Lejos':
                        $cantidadLejos = $informeOT['cantidadCristales'];
                        break;
                    case 'Cerca':
                        $cantidadCerca = $informeOT['cantidadCristales'];
                        break;
                    case 'Bifocal':
                        $cantidadBifocal = $informeOT['cantidadCristales'];
                        break;
                    case 'Ocupacional':
                        $cantidadOcupacional = $informeOT['cantidadCristales'];
                        break;
                    case 'Progresivo':
                        $cantidadProgresivo = $informeOT['cantidadCristales'];
                        break;
                }
            }
        }

        // Armo el string con el último elemento del arreglo
        // ya que este entra por false en el if
        $cristales = "Lejos ($cantidadLejos), Cerca ($cantidadCerca), 
        Bifocal ($cantidadBifocal), Ocupacional ($cantidadOcupacional),
        Progresivo ($cantidadProgresivo)";

        // Agrego el string al arreglo
        $arregloCristales[$idAnterior] = $cristales;

        $OTsTemplate = 'informe/colegioOpticoOT_imprimir.html.twig';
        $html = $this->renderView($OTsTemplate, array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'informeOTs' => $informeOTs,
            'ordenes_trabajo' => $ordenes_trabajo,
            'sucursal_id' => $sucursalId,
            'sucursal' => $sucursal,
            'arreglo_cristales' => $arregloCristales
        ));

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "informe_colegioMedico_OT";
        $pdf->Output($filename.".pdf",'I');

        return true;
    }



    // Informe Colegio de Ópticos OT contactología
    public function colegioOpticoOTcontactologiaAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('otc.id as id_OT, otc.id as id_OrdenTrabajo, cli.nombre as nombreCliente, cli.direccion, 
        otc.fechaReceta, med.nombre as medicoNombre')
            ->from(OrdenTrabajoContactologia::class, 'otc')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = otc.cliente')
            ->leftJoin('otc.medico', 'med')
            ->where('otc.fechaReceta >= :fechaDesde')
            ->andWhere('otc.fechaReceta <= :fechaHasta')
            ->orderBy('otc.fechaReceta')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query
                ->leftJoin('otc.sucursal', 'sucursal')
                ->andWhere('sucursal.id = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $informeOTscontactologia = $query->getQuery()->getResult();

        return $this->render('informe/colegioOpticoOTcontactologia.html.twig', array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'informeOTscontactologia' => $informeOTscontactologia,
            'sucursal_id' => $sucursalId,
            'sucursales' => $sucursales,
        ));
    }

    /**
     * Reporte Colegio de Ópticos OT
     */
    public function colegioOpticoOTcontactologia_imprimirAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder
            ->select('otc.id as id_OT, otc.id as id_OrdenTrabajo, cli.nombre as nombreCliente, cli.direccion, 
            otc.fechaReceta, med.nombre as medicoNombre, med.matricula as medicoMatricula,
            COUNT(otcd.id) as cantidadCristales, otcd.tipoCristal, otc.fechaRecepcion, 
            otc.fechaEntrega')
            ->from(OrdenTrabajoContactologia::class, 'otc')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = otc.cliente')
            ->leftJoin('otc.medico', 'med')
            ->leftJoin(OrdenTrabajoContactologiaDetalle::class, 'otcd', 'WITH', 'otcd.ordenTrabajoContactologia = otc.id')
            ->where('otc.fechaReceta >= :fechaDesde')
            ->andWhere('otc.fechaReceta <= :fechaHasta')
            ->orderBy('otc.fechaReceta')
            ->groupBy('otc.id, otcd.tipoCristal')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        $informeOTscontactologia = $query->getQuery()->getResult();

        $em = $this->getDoctrine()->getManager();
        $queryBuilder2 = $em->createQueryBuilder();
        $query2 = $queryBuilder2
            ->select('otc.id, otc.fechaReceta, cli.nombre as nombreCliente, cli.direccion, med.nombre as medicoNombre,
            med.matricula as medicoMatricula, otc.lejosOjoDerechoEsfera, otc.lejosOjoDerechoCilindro, otc.lejosOjoDerechoEje,
            otc.lejosOjoIzquierdoEsfera, otc.lejosOjoIzquierdoCilindro, otc.lejosOjoIzquierdoEje,
            otc.cercaOjoDerechoEsfera, otc.cercaOjoDerechoCilindro, otc.cercaOjoDerechoEje,
            otc.cercaOjoIzquierdoEsfera, otc.cercaOjoIzquierdoCilindro, otc.cercaOjoIzquierdoEje,
			otc.rcOjoDerechoHorizontal, otc.rcOjoDerechoVertical, otc.rcOjoIzquierdoHorizontal,
            otc.rcOjoIzquierdoVertical, otc.ojoDerechoCurvas, otc.ojoDerechoDiametro, otc.ojoDerechoCaracteristicas, 
            otc.ojoDerechoAV, otc.ojoIzquierdoCurvas, otc.ojoIzquierdoDiametro, otc.ojoIzquierdoCaracteristicas,
            otc.ojoIzquierdoAV, otc.fechaRecepcion, otc.fechaEntrega, GROUP_CONCAT(marco.descripcion SEPARATOR \', \') as descripcionMarcos')
            ->from(OrdenTrabajoContactologia::class, 'otc')
            ->leftJoin(Cliente::class, 'cli', 'WITH', 'cli.id = otc.cliente')
            ->leftJoin('otc.medico', 'med')
            ->leftJoin(OrdenTrabajoDetalle::class, 'otcd', 'WITH', 'otcd.ordenTrabajo = otc.id')
            ->leftJoin('otcd.articulo', 'a') // Relacionar con la tabla de artículo
            ->leftJoin('a.marco', 'marco')
            ->where('otc.fechaReceta >= :fechaDesde')
            ->andWhere('otc.fechaReceta <= :fechaHasta')
            ->orderBy('otc.fechaReceta')
            ->groupBy('otc.id, otc.fechaReceta, cli.nombre, cli.direccion, med.nombre, med.matricula, otc.lejosOjoDerechoEsfera, otc.lejosOjoDerechoCilindro, otc.lejosOjoDerechoEje, otc.lejosOjoIzquierdoEsfera, otc.lejosOjoIzquierdoCilindro, otc.lejosOjoIzquierdoEje, otc.cercaOjoDerechoEsfera, otc.cercaOjoDerechoCilindro, otc.cercaOjoDerechoEje, otc.cercaOjoIzquierdoEsfera, otc.cercaOjoIzquierdoCilindro, otc.cercaOjoIzquierdoEje, otc.rcOjoDerechoHorizontal, otc.rcOjoDerechoVertical, otc.rcOjoIzquierdoHorizontal, otc.rcOjoIzquierdoVertical, otc.ojoDerechoCurvas, otc.ojoDerechoDiametro, otc.ojoDerechoCaracteristicas, otc.ojoDerechoAV, otc.ojoIzquierdoCurvas, otc.ojoIzquierdoDiametro, otc.ojoIzquierdoCaracteristicas, otc.fechaRecepcion, otc.fechaEntrega')
            ->setParameter('fechaDesde', $fechaDesde)
            ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query2
                ->leftJoin('otc.sucursal', 'sucursal')
                ->andWhere('sucursal.id = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
            $sucursal = $em->getRepository(Sucursal::class)->find($sucursalId);
        }

        $ordenes_trabajo_contactologia = $query2->getQuery()->getResult();

        $idAnterior = null;
        $cantidadLejos = 0;
        $cantidadMonoVision = 0;
        $cantidadMultifocal = 0;
        $arregloCristales = array();

        // Recorro los resultados de la consulta para obtener las cantidades de los
        // diferentes tipos de cristales que correspondan a la misma otc
        foreach ($informeOTscontactologia as $informeOTcontactologia ){
            $idActual = $informeOTcontactologia['id_OT'];
            $tipoCristal = $informeOTcontactologia['tipoCristal'];
            if ($idActual == $idAnterior){
                switch($tipoCristal){
                    case 'Lejos':
                        $cantidadLejos = $informeOTcontactologia['cantidadCristales'];
                        break;
                    case 'Mono Visión':
                        $cantidadMonoVision = $informeOTcontactologia['cantidadCristales'];
                        break;
                    case 'Multifocal':
                        $cantidadMultifocal = $informeOTcontactologia['cantidadCristales'];
                        break;
                    default:
                        break;
                }
            }
            else{
                // Armo string
                $cristales = "Lejos ($cantidadLejos), Mono Visión ($cantidadMonoVision), 
                    Multifocal ($cantidadMultifocal)";

                // Agrego el string al arreglo
                $arregloCristales[$idAnterior] = $cristales;

                // Reinicio contadores
                $idAnterior = $idActual;
                $cantidadLejos = 0;
                $cantidadMonoVision = 0;
                $cantidadMultifocal = 0;

                // Asignar cantidad del primer informe que no pasa por true
                // ya que la variable IdAnterior se inicializa en null
                switch($tipoCristal){
                    case 'Lejos':
                        $cantidadLejos = $informeOTcontactologia['cantidadCristales'];
                        break;
                    case 'Mono Visión':
                        $cantidadMonoVision = $informeOTcontactologia['cantidadCristales'];
                        break;
                    case 'Multifocal':
                        $cantidadMultifocal = $informeOTcontactologia['cantidadCristales'];
                        break;
                    default:
                        break;
                }
            }
        }

        // Armo el string con el último elemento del arreglo
        // ya que este entra por false en el if
        $cristales = "Lejos ($cantidadLejos), Mono Visión ($cantidadMonoVision), 
        Multifocal ($cantidadMultifocal)";

        // Agrego el string al arreglo
        $arregloCristales[$idAnterior] = $cristales;

        $otcsTemplate = 'informe/colegioOpticoOTcontactologia_imprimir.html.twig';
        $html = $this->renderView($otcsTemplate, array(
            'fecha_desde' => $fechaDesde->format('d-m-Y'),
            'fecha_hasta' => $fechaHasta->format('d-m-Y'),
            'informeOTscontactologia' => $informeOTscontactologia,
            'ordenes_trabajo_contactologia' => $ordenes_trabajo_contactologia,
            'sucursal_id' => $sucursalId,
            'sucursal' => $sucursal,
            'arreglo_cristales' => $arregloCristales
        ));

        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "informe_colegioMedico_OT";
        $pdf->Output($filename.".pdf",'I');

        return true;
    }

}
