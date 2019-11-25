<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Stock;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Stock controller.
 *
 */
class StockController extends Controller
{
    /**
     * Lists all stock entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $rol = $this->getUser()->getRol();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Stock')->findByTexto($texto);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('stock/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto,
            'rol' => $rol->getDescripcion()
        ));
    }

    /**
     * Creates a new stock entity.
     *
     */
    public function newAction(Request $request)
    {
        $stock = new Stock();
        $form = $this->createForm('AppBundle\Form\StockType', $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $stockdestino = $em->getRepository('AppBundle:Stock')->findBy(array('articulo' => $stock->getArticulo(), 'sucursal' => $stock->getSucursal()));

            if (count($stockdestino) == 0)
            {
                $cantidadMinima = $em->getRepository('AppBundle:Articulo')->find($stock->getArticulo())->getCantidadMinima();
                $stock->setCantidadMinima($cantidadMinima);
                $stock->setActivo(true);
                $stock->setCreatedBy($this->getUser());
                $stock->setCreatedAt(new \DateTime("now"));
                $stock->setUpdatedBy($this->getUser());
                $stock->setUpdatedAt(new \DateTime("now"));

                $em->persist($stock);
                $em->flush();
            }
            else
            {
                $this->get('session')->getFlashbag()->add('warning', 'El Articulo ya posee stock, Usted fue redireccinado al modo Edición');

                $editForm = $this->createForm('AppBundle\Form\StockType', $stock);
                $editForm->handleRequest($request);

                return $this->redirectToRoute('stock_edit', array('id' => $stockdestino[0]->getId()));
            }

            return $this->redirectToRoute('stock_show', array('id' => $stock->getId()));
        }

        return $this->render('stock/new.html.twig', array(
            'stock' => $stock,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a stock entity.
     *
     */
    public function showAction(Stock $stock)
    {
        $deleteForm = $this->createDeleteForm($stock);

        return $this->render('stock/show.html.twig', array(
            'stock' => $stock,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing stock entity.
     *
     */
    public function editAction(Request $request, Stock $stock)
    {
        $deleteForm = $this->createDeleteForm($stock);
        $editForm = $this->createForm('AppBundle\Form\StockType', $stock);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Obtengo datos "Not Mapped" (Form)
            $sucursalDestino = $editForm->get("sucursaldestino")->getData();
            $moverstock = $editForm->get("moverstock")->getData();

            if ($sucursalDestino!=null && $sucursalDestino <> $stock->getSucursal() && $moverstock > 0 && $moverstock <= $stock->getCantidad())
            {
                // Verificar si la sucursal destino tiene stock (si no tiene hay que crear la row)
                $stockdestino = $em->getRepository('AppBundle:Stock')->findBy(array('articulo' => $stock->getArticulo(), 'sucursal' => $sucursalDestino))[0];

                // Si el registro existe en la tabla Stock, actualizo. 
                // Else lo creo
                if (count($stockdestino) > 0)
                {
                    $stockdestino->setCantidad($stockdestino->getCantidad() + $moverstock);
                    $stock->setCantidad($stock->getCantidad() - $moverstock);                    
                }
                else
                {
                    $stockdestino = new Stock();
                    $cantidadMinima = $em->getRepository('AppBundle:Articulo')->find($stock->getArticulo())->getCantidadMinima();

                    $stockdestino->setArticulo($stock->getArticulo());
                    $stockdestino->setSucursal($sucursalDestino);
                    $stockdestino->setCantidad($moverstock);
                    $stockdestino->setCantidadMinima($cantidadMinima);
                    $stockdestino->setActivo(true);
                    $stockdestino->setCreatedBy($this->getUser());
                    $stockdestino->setCreatedAt(new \DateTime("now"));
                    $stockdestino->setUpdatedBy($this->getUser());
                    $stockdestino->setUpdatedAt(new \DateTime("now"));

                    $stock->setCantidad($stock->getCantidad() - $moverstock);
                }

                $em->persist($stock);
                $em->persist($stockdestino);
            }

            $em->flush();

            return $this->redirectToRoute('stock_show', array('id' => $stock->getId()));
        }

        return $this->render('stock/edit.html.twig', array(
            'stock' => $stock,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a stock entity.
     *
     */
    public function deleteAction(Request $request, Stock $stock)
    {
        $form = $this->createDeleteForm($stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stock);
            $em->flush();
        }

        return $this->redirectToRoute('stock_index');
    }

    /**
     * Creates a form to delete a stock entity.
     *
     * @param Stock $stock The stock entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Stock $stock)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stock_delete', array('id' => $stock->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
