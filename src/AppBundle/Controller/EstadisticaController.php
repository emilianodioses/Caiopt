<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;

/**
 * Articulo controller.
 *
 */
class EstadisticaController extends Controller
{
    /**
     * Lists all articulo entities.
     *
     */
    public function ventasAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        return $this->render('estadistica/ventas.html.twig', array(
            'ventasXCategoria' => $this->ventasXCategoria($request),
            'ventasXVendedor' => $this->ventasXVendedor($request),
            'ventasXMes' => $this->ventasXMes($request),
            'ventasXSucursal' => $this->ventasXSucursal($request),
            'ventasXMediosPago' => $this->ventasXMediosPago($request),
            'ventasXMedico' => $this->ventasXMedico($request),
            'ordenesTrabajoXUsuario' => $this->ordenesTrabajoXUsuario($request),
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'sucursal_id' => $sucursalId,
            'usuarios' => $usuarios,
            'sucursales' => $sucursales,
            //'texto' => $texto
        ));
    }

    private function ventasXMediosPago(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXMediosPago = $em->getRepository('AppBundle:ClientePago')
        ->createQueryBuilder('clientepago')
        ->join('clientepago.pagoTipo','pagoTipo')
        ->join('clientepago.recibo','recibo')
        ->select('pagoTipo.nombre as medio, SUM(clientepago.importe) as total')
        ->where('recibo.fecha >= :fechaDesde')
        ->andWhere('recibo.fecha <= :fechaHasta')
        ->andWhere('recibo.activo = true')
        ->groupBy('medio')
        ->orderBy('total', 'DESC')    
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $ventasXMediosPago = $ventasXMediosPago
                ->andWhere('recibo.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $ventasXMediosPago = $ventasXMediosPago->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Medio de Pago', 'Total');
        array_push($data, $header);

        foreach($ventasXMediosPago as $medio) {
            $item = array($medio["medio"], (int)$medio["total"]);
            array_push($data, $item);
        }
        
        $ventasXMediosPago = new PieChart();
        $ventasXMediosPago->getData()->setArrayToDataTable($data);

        $ventasXMediosPago->getOptions()->setTitle('Ventas por Medios de Pagos');
        $ventasXMediosPago->getOptions()->setHeight(400);
        //$ventasXMediosPago->getOptions()->setWidth(300);
        $ventasXMediosPago->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXMediosPago->getOptions()->setis3D(true);
        $ventasXMediosPago->getOptions()->getLegend()->setPosition('left');
        $ventasXMediosPago->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventasXMediosPago->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventasXMediosPago->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventasXMediosPago->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventasXMediosPago;
    // Google Charts 
    }

    private function ventasXSucursal(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXSucursal = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->join('comprobante.sucursal','sucursal')
        ->select('sucursal.nombre as sucursalNombre, SUM(comprobante.total) as total')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->andWhere('comprobante.activo = true')
        ->groupBy('sucursalNombre')
        ->orderBy('total', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $ventasXSucursal = $ventasXSucursal
                ->andWhere('sucursal.id = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $ventasXSucursal = $ventasXSucursal->getQuery()
        ->getArrayResult();

        $data = array();
        $header = array('Sucursal', 'Total');
        array_push($data, $header);

        foreach($ventasXSucursal as $sucursal) {
            $item = array($sucursal["sucursalNombre"], (int)$sucursal["total"]);
            array_push($data, $item);
        }
        
        $ventasXSucursal = new PieChart();
        $ventasXSucursal->getData()->setArrayToDataTable($data);

        $ventasXSucursal->getOptions()->setTitle('Ventas por Sucursal');
        $ventasXSucursal->getOptions()->setHeight(400);
        //$ventasXSucursal->getOptions()->setWidth(300);
        $ventasXSucursal->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXSucursal->getOptions()->setis3D(true);
        $ventasXSucursal->getOptions()->getLegend()->setPosition('left');
        $ventasXSucursal->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventasXSucursal->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventasXSucursal->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventasXSucursal->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventasXSucursal;
    // Google Charts 
    }

    private function ventasXCategoria(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasCategoria = $em->getRepository('AppBundle:ComprobanteDetalle')
        ->createQueryBuilder('comprobantedetalle')
        ->join('comprobantedetalle.comprobante','comprobante')
        ->join('comprobantedetalle.articulo','articulo')
        ->join('articulo.categoria','articuloCategoria')
        ->join('articulo.marca','articuloMarca')
        ->select('articuloCategoria.descripcion as categoria, SUM(comprobantedetalle.total) as sumaTotal')
        ->where('comprobantedetalle.movimiento = :movimiento')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->andWhere('comprobante.activo = true')
        ->andWhere('comprobantedetalle.activo = true')
        ->groupBy('categoria')
        ->orderBy('sumaTotal', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $ventasCategoria = $ventasCategoria
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $ventasCategoria = $ventasCategoria->getQuery()
        ->getArrayResult();

        $data = array();
        $header = array('Categoria', 'Suma Total');
        array_push($data, $header);

        foreach($ventasCategoria as $categoria) {
            $item = array($categoria["categoria"], (int)$categoria["sumaTotal"]);
            array_push($data, $item);
        }
        
        $ventaCategoria = new PieChart();
        $ventaCategoria->getData()->setArrayToDataTable($data);

        $ventaCategoria->getOptions()->setTitle('Ventas por Categoria en $');
        $ventaCategoria->getOptions()->setHeight(400);
        //$ventaCategoria->getOptions()->setWidth(300);
        $ventaCategoria->getOptions()->getTitleTextStyle()->setBold(true);
        $ventaCategoria->getOptions()->setis3D(true);
        $ventaCategoria->getOptions()->getLegend()->setPosition('left');
        $ventaCategoria->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventaCategoria->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventaCategoria->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventaCategoria->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventaCategoria;
    // Google Charts 
    }

    private function ventasXVendedor(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXVendedor = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->join('comprobante.usuario','usuario')
        ->select('usuario.usuario, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('usuario')
        ->orderBy('sumaTotal', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $ventasXVendedor = $ventasXVendedor
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $ventasXVendedor = $ventasXVendedor->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Vendedor', 'Suma Total');
        array_push($data, $header);

        foreach($ventasXVendedor as $vendedor) {
            $item = array($vendedor["usuario"], (int)$vendedor["sumaTotal"]);
            array_push($data, $item);
        }
        
        $ventasXVendedor = new PieChart();
        $ventasXVendedor->getData()->setArrayToDataTable($data);

        $ventasXVendedor->getOptions()->setTitle('Ventas por Vendedor en $');
        $ventasXVendedor->getOptions()->setHeight(400);
        //$ventasXVendedor->getOptions()->setWidth(300);
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXVendedor->getOptions()->setis3D(true);
        $ventasXVendedor->getOptions()->getLegend()->setPosition('left');
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventasXVendedor;
    // Google Charts 
    }

    private function ventasXMes(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $query = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->select('MONTH(comprobante.fecha) as mes, YEAR(comprobante.fecha) as anio, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('anio')
        ->addGroupBy('mes')
        ->orderBy('anio', 'ASC')
        ->addOrderBy('mes', 'ASC')
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Mes', 'Total $');
        array_push($data, $header);

        foreach($query as $mes) {
            $item = array($mes["mes"]."-".$mes["anio"], (int)$mes["sumaTotal"]);
            array_push($data, $item);
        }

        $chart = new ColumnChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('Ventas por Mes en $');
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

    private function ventasXMedico(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $query = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->join('comprobante.usuario','usuario')
        ->join('comprobante.ordenTrabajo', 'ordenTrabajo')
        ->join('ordenTrabajo.medico', 'medico')
        ->select('medico.nombre as medico_nombre, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('medico')
        ->orderBy('sumaTotal', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Médico', 'Total $');
        array_push($data, $header);

        foreach($query as $vendedor) {
            $item = array($vendedor["medico_nombre"], (int)$vendedor["sumaTotal"]);
            array_push($data, $item);
        }

        $chart = new ColumnChart();
        $chart->getData()->setArrayToDataTable($data);
        
        $chart->getOptions()->setTitle('Ventas por Medico en $');
        $chart->getOptions()->setHeight(500);
        //$chart->getOptions()->setWidth(300);
        $chart->getOptions()->getHAxis()->setTitle('Médico');
        $chart->getOptions()->getHAxis()->setSlantedText(true);
        $chart->getOptions()->getHAxis()->setSlantedTextAngle('90');
        $chart->getOptions()->getHAxis()->getTextStyle()->setFontSize(10);
        $chart->getOptions()->getChartArea()->setHeight('40%');
        $chart->getOptions()->getChartArea()->setTop('70');
        //$chart->getOptions()->getLegend()->setPosition('left');
        $chart->getOptions()->getLegend()->setAlignment('vertical');
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(15);
        
        /*
        $chart = new PieChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('Ventas por Vendedor en $');
        $chart->getOptions()->setHeight(400);
        //$chart->getOptions()->setWidth(300);
        $chart->getOptions()->getTitleTextStyle()->setBold(true);
        $chart->getOptions()->setis3D(true);
        $chart->getOptions()->getLegend()->setPosition('left');
        $chart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $chart->getOptions()->getTitleTextStyle()->setItalic(true);
        $chart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $chart->getOptions()->getTitleTextStyle()->setFontSize(15);
        */
        return $chart;
    // Google Charts 
    }

    private function ordenesTrabajoXUsuario(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde')." 00:00:00");
        $fechaHasta = new \DateTime($request->get('fecha_hasta')." 23:59:59");
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $query = $em->getRepository('AppBundle:OrdenTrabajo')
        ->createQueryBuilder('ordenTrabajo')
        ->join('ordenTrabajo.createdBy','usuario')
        ->select('usuario.usuario, COUNT(ordenTrabajo.id) as cantidadOT')
        ->Where('ordenTrabajo.activo = 1')
        ->andWhere('ordenTrabajo.createdAt >= :fechaDesde')
        ->andWhere('ordenTrabajo.createdAt <= :fechaHasta')
        ->groupBy('usuario')
        ->orderBy('cantidadOT', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $query = $query
                ->andWhere('ordenTrabajo.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $query = $query->getQuery()
            ->getArrayResult();

        $data = array();
        $header = array('Usuario', 'Cantidad OT');
        array_push($data, $header);

        foreach($query as $vendedor) {
            $item = array($vendedor["usuario"], (int)$vendedor["cantidadOT"]);
            array_push($data, $item);
        }
        
        $chart = new PieChart();
        $chart->getData()->setArrayToDataTable($data);

        $chart->getOptions()->setTitle('Cantidad de Ordenes de Trabajo por Usuario');
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
}