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

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();

        return $this->render('estadistica/ventas.html.twig', array(
            'ventasXCategoria' => $this->ventasXCategoria($request),
            'ventasXVendedor' => $this->ventasXVendedor($request),
            'ventasXMes' => $this->ventasXMes($request),
            'usuarios' => $usuarios
            //'texto' => $texto
        ));
    }

    private function ventasXCategoria(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fechaDesde = new \DateTime($request->get('fecha_desde'));
        $fechaHasta = new \DateTime($request->get('fecha_hasta'));
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasCategoria = $em->getRepository('AppBundle:ComprobanteDetalle')
        ->createQueryBuilder('comprobantedetalle')
        ->join('comprobantedetalle.comprobante','comprobante')
        ->join('comprobantedetalle.articulo','articulo')
        ->join('articulo.categoria','articuloCategoria')
        ->join('articulo.marca','articuloMarca')
        ->select('articuloCategoria.descripcion as categoria, SUM(comprobantedetalle.cantidad) as cantidad')
        ->where('comprobantedetalle.movimiento = :movimiento')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('categoria')
        ->orderBy('cantidad', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta)
        ->getQuery()
        ->getArrayResult();

        $datapie = array();
        $header = array('Categoria', 'Cantidad');
        array_push($datapie, $header);

        foreach($ventasCategoria as $categoria) {
            $item = array($categoria["categoria"], (int)$categoria["cantidad"]);
            array_push($datapie, $item);
        }
        
        $ventaCategoria = new PieChart();
        $ventaCategoria->getData()->setArrayToDataTable($datapie);

        $ventaCategoria->getOptions()->setTitle('Ventas por Categoria');
        $ventaCategoria->getOptions()->setHeight(300);
        $ventaCategoria->getOptions()->setWidth(300);
        $ventaCategoria->getOptions()->getTitleTextStyle()->setBold(true);
        $ventaCategoria->getOptions()->setis3D(true);
        $ventaCategoria->getOptions()->getLegend()->setPosition('top');
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
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXVendedor = $em->getRepository('AppBundle:ComprobanteDetalle')
        ->createQueryBuilder('comprobantedetalle')
        ->join('comprobantedetalle.comprobante','comprobante')
        ->join('comprobante.usuario','usuario')
        ->select('usuario.usuario, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobantedetalle.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('usuario')
        ->orderBy('sumaTotal', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta)
        ->getQuery()
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
        $ventasXVendedor->getOptions()->setHeight(300);
        $ventasXVendedor->getOptions()->setWidth(300);
        $ventasXVendedor->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXVendedor->getOptions()->setis3D(true);
        $ventasXVendedor->getOptions()->getLegend()->setPosition('top');
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
        //$usuarioId = $request->get('usuario');

        // Google Charts - Venta por Categoria
        $ventasXMes = $em->getRepository('AppBundle:ComprobanteDetalle')
        ->createQueryBuilder('comprobantedetalle')
        ->join('comprobantedetalle.comprobante','comprobante')
        ->select('MONTH(comprobante.fecha) as mes, SUM(comprobante.total) as sumaTotal')
        ->where('comprobante.movimiento = :movimiento')
        ->andWhere('comprobante.activo = 1')
        ->andWhere('comprobantedetalle.activo = 1')
        ->andWhere('comprobante.fecha >= :fechaDesde')
        ->andWhere('comprobante.fecha <= :fechaHasta')
        ->groupBy('mes')
        ->orderBy('sumaTotal', 'DESC')    
        //->setMaxResults(10)
        ->setParameter('movimiento', "Venta")
        ->setParameter('fechaDesde', $fechaDesde)
        ->setParameter('fechaHasta', $fechaHasta)
        ->getQuery()
        ->getArrayResult();

        $datapie = array();
        $header = array('Vendedor', 'Suma Total');
        array_push($datapie, $header);

        foreach($ventasXMes as $vendedor) {
            $item = array($vendedor["mes"], (int)$vendedor["sumaTotal"]);
            array_push($datapie, $item);
        }
        
        $ventasXMes = new PieChart();
        $ventasXMes->getData()->setArrayToDataTable($datapie);

        $ventasXMes->getOptions()->setTitle('Ventas por Mes en $');
        $ventasXMes->getOptions()->setHeight(300);
        $ventasXMes->getOptions()->setWidth(300);
        $ventasXMes->getOptions()->getTitleTextStyle()->setBold(true);
        $ventasXMes->getOptions()->setis3D(true);
        $ventasXMes->getOptions()->getLegend()->setPosition('top');
        $ventasXMes->getOptions()->getTitleTextStyle()->setColor('#009900');
        $ventasXMes->getOptions()->getTitleTextStyle()->setItalic(true);
        $ventasXMes->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $ventasXMes->getOptions()->getTitleTextStyle()->setFontSize(15);

        return $ventasXMes;
    // Google Charts 
    }
}