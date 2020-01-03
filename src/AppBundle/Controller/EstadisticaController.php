<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

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

        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
        $sucursalId = $request->get('sucursal');

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        $sucursales = $em->getRepository('AppBundle:Sucursal')->findAll();

        return $this->render('estadistica/ventas.html.twig', array(
            'ventasXCategoria' => $this->ventasXCategoria($request),
            'ventasXVendedor' => $this->ventasXVendedor($request),
            'ventasXMes' => $this->ventasXMes($request),
            'ventasXSucursal' => $this->ventasXSucursal($request),
            'ventasXMediosPago' => $this->ventasXMediosPago($request),
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
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
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

        $datapie = array();
        $header = array('Medio de Pago', 'Total');
        array_push($datapie, $header);

        foreach($ventasXMediosPago as $medio) {
            $item = array($medio["medio"], (int)$medio["total"]);
            array_push($datapie, $item);
        }
        
        $ventasXMediosPago = new PieChart();
        $ventasXMediosPago->getData()->setArrayToDataTable($datapie);

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
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
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

        $datapie = array();
        $header = array('Sucursal', 'Total');
        array_push($datapie, $header);

        foreach($ventasXSucursal as $sucursal) {
            $item = array($sucursal["sucursalNombre"], (int)$sucursal["total"]);
            array_push($datapie, $item);
        }
        
        $ventasXSucursal = new PieChart();
        $ventasXSucursal->getData()->setArrayToDataTable($datapie);

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
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
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

        $datapie = array();
        $header = array('Categoria', 'Suma Total');
        array_push($datapie, $header);

        foreach($ventasCategoria as $categoria) {
            $item = array($categoria["categoria"], (int)$categoria["sumaTotal"]);
            array_push($datapie, $item);
        }
        
        $ventaCategoria = new PieChart();
        $ventaCategoria->getData()->setArrayToDataTable($datapie);

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
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
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

        $datapie = array();
        $header = array('Vendedor', 'Suma Total');
        array_push($datapie, $header);

        foreach($ventasXVendedor as $vendedor) {
            $item = array($vendedor["usuario"], (int)$vendedor["sumaTotal"]);
            array_push($datapie, $item);
        }
        
        $ventasXVendedor = new PieChart();
        $ventasXVendedor->getData()->setArrayToDataTable($datapie);

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
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
        $sucursalId = $request->get('sucursal');
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXMes = $em->getRepository('AppBundle:Comprobante')
        ->createQueryBuilder('comprobante')
        ->select('MONTH(comprobante.fecha) as mes, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('mes')
        ->orderBy('sumaTotal', 'DESC')
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta);

        if ($sucursalId > 0) {
            $ventasXMes = $ventasXMes
                ->andWhere('comprobante.sucursal = :sucursalId')
                ->setParameter('sucursalId', $sucursalId);
        }

        $ventasXMes = $ventasXMes->getQuery()
            ->getArrayResult();

        $datapie = array();
        $header = array('Mes', 'Suma Total');
        array_push($datapie, $header);

        foreach($ventasXMes as $mes) {
            $item = array($mes["mes"], (int)$mes["sumaTotal"]);
            array_push($datapie, $item);
        }
        
        $ventasXMes = new PieChart();
        $ventasXMes->getData()->setArrayToDataTable($datapie);

        $ventasXMes->getOptions()->setTitle('Ventas por Mes en $');
        $ventasXMes->getOptions()->setHeight(400);
        //$ventasXMes->getOptions()->setWidth(300);
        $ventasXMes->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXMes->getOptions()->setis3D(true);
        $ventasXMes->getOptions()->getLegend()->setPosition('left');
        $ventasXMes->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventasXMes->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventasXMes->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventasXMes->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventasXMes;
    // Google Charts 
    }
}