<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Articulo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Stock;

/**
 * Articulo controller.
 *
 */
class ArticuloController extends Controller
{
    /**
     * Lists all articulo entities.
     *
     */
    public function indexAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Articulo')->findByTexto($texto);

        //$query = $em->getRepository('AppBundle:Articulo')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('articulo/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new articulo entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $articulo = new Articulo();
        $form = $this->createForm('AppBundle\Form\ArticuloType', $articulo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $articulo->setPrecioModifica(1);
            $articulo->setOrdenTrabajo(1);
            $articulo->setUltimoComprobante(null);
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

            $em->flush();

            return $this->redirectToRoute('articulo_show', array('id' => $articulo->getId()));
        }

        return $this->render('articulo/new.html.twig', array(
            'articulo' => $articulo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a articulo entity.
     *
     */
    public function showAction(Articulo $articulo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($articulo);

        $em = $this->getDoctrine()->getManager();
        $stockSucursales = $em->getRepository('AppBundle:Stock')->findBy(array('articulo' => $articulo));
        $ventasSucursales = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(array('articulo' => $articulo, 'movimiento' =>  'Venta'));

        $stock = 0;
        $ventas = 0;

        foreach ($stockSucursales as $stockSucursal) {
            $stock = $stock + $stockSucursal->getCantidad();
        }

        foreach ($ventasSucursales as $ventasSucursales) {
            $ventas = $ventas + $ventasSucursales->getCantidad();
        }

        return $this->render('articulo/show.html.twig', array(
            'articulo' => $articulo,
            'ventas' => $ventas,
            'stock' => $stock,
            'stockSucursales' => $stockSucursales,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing articulo entity.
     *
     */
    public function editAction(Request $request, Articulo $articulo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $deleteForm = $this->createDeleteForm($articulo);
        $editForm = $this->createForm('AppBundle\Form\ArticuloType', $articulo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $articulo->setPrecioModifica(1);
            $articulo->setOrdenTrabajo(1);
            $articulo->setUltimoComprobante(null);
            $articulo->setUpdatedBy($this->getUser());
            $articulo->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('articulo_show', array('id' => $articulo->getId()));
        }

        return $this->render('articulo/edit.html.twig', array(
            'articulo' => $articulo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a articulo entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $articulo = $em->getRepository('AppBundle:Articulo')->find($id);
        if ($articulo->getActivo() > 0)
            $articulo->setActivo(0);
        else
            $articulo->setActivo(1);  

        $articulo->setUpdatedBy($this->getUser()); 
        $articulo->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush();
        
        return $this->redirectToRoute('articulo_index');
    }

    /**
     * Creates a form to delete a articulo entity.
     *
     * @param Articulo $articulo The articulo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Articulo $articulo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('articulo_delete', array('id' => $articulo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function findAction(Request $req) {
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(0);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, array('json' => new JsonEncoder()));
        
        $articulo = $this->getDoctrine()->getManager('default')->getRepository('AppBundle:Articulo')
                ->find($req->get('articuloId'));

        $articulo_array = array();
        $articulo_array['precioVenta'] = $articulo->getPrecioVenta();
        $articulo_array['iva'] = $articulo->getIva()->getDescripcion();
        $articulo_array['precioCosto'] = $articulo->getPrecioCosto();
        $articulo_array['gananciaPorcentaje'] = $articulo->getGananciaPorcentaje();
        //$articulo_array['']

        $j_articulo = $serializer->serialize($articulo_array, 'json');
        
        return JsonResponse::create(array('articulo' => $j_articulo));
    }

    public function findSelect2Action(Request $request) {
        $em = $em = $this->getDoctrine()->getManager('default');
        
        $text_search = $request->get('q');
        $pageLimit = $request->get('page_limit');

        if (!is_numeric($pageLimit) || $pageLimit > 10) {
            $pageLimit = 10;
        }

        $result = $em->createQuery('
                        SELECT r.id as id, r.descripcion as text
                        FROM AppBundle:Articulo r
                        INNER JOIN r.marca am
                        WHERE r.activo = 1 AND (
                            lower(r.descripcion) LIKE :text_search  OR 
                            lower(r.codigo) LIKE :text_search  OR 
                            lower(am.descripcion) LIKE :text_search
                        )
                        ORDER BY r.descripcion ASC
                        ')
                    ->setParameter('text_search', '%'.$text_search.'%')
                    ->setMaxResults($pageLimit)
                ->getArrayResult();
        
        return new JsonResponse($result);
    }

    public function articuloAjusteAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('Articulo', 'AjustePrecio', $this->getUser()->getRol())):
            $response = new Response('<b>Acceso denegado:</b>
                <br>Solicite acceso a su administrador: Articulos - Ajuste Precios');
            
        $response->setContent($response->getContent() . '<br><a href="javascript:history.back()">Volver atrás</a>');
        
        return $response;
        endif;

        $em = $this->getDoctrine()->getManager();
        $marcas = $em->getRepository('AppBundle:ArticuloMarca')->findBy(array('activo' => true));
        $categorias = $em->getRepository('AppBundle:ArticuloCategoria')->findBy(array('activo' => true));
        $marca = $request->query->get('marca', 0);
        $categoria = $request->query->get('categoria', 0);

        if (isset($_GET['ajustec'])) {
            $ajuste = $request->query->get('ajuste', 0);
            $em->getRepository('AppBundle:Articulo')->updateByAjuste($marca,$categoria,$ajuste);
        }
        $articulos = $em->getRepository('AppBundle:Articulo')->findByAjuste($marca,$categoria);

        return $this->render('articulo/articuloAjuste.html.twig', array(
            'marcas' => $marcas,
            'categorias' => $categorias,
            'articulos' => $articulos,
            'marcaSel' => $marca,
            'categoriaSel' => $categoria
        ));
    }
}