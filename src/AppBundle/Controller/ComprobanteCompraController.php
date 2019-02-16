<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\ComprobanteType;
use AppBundle\Form\ComprobanteDetalleType;

/**
 * Comprobante controller.
 *
 */
class ComprobanteCompraController extends controller
{
    /**
     * Lists all comprobante entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento'=>'compra', 'activo'=> '1'));
        
        return $this->render('comprobantecompra/index.html.twig', array(
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
        $form = $this->createForm('AppBundle\Form\ComprobanteType', $comprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();

            //INICIO Validacion Comprobante Existente
            $comprobanteDuplicado = $em->getRepository('AppBundle:Comprobante')->findBy(Array('numero'=>$comprobante->getNumero(),  'activo'=>1, 'movimiento' => 'Compra'));

            if (count($comprobanteDuplicado) > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'El Comprobante ya fue cargado');

                return $this->render('comprobantecompra/new.html.twig', array(
                    'comprobante' => $comprobante,
                    'form' => $form->createView(),
                ));
            }
            //FIN Validacion Comprobante Existente

            $comprobante->setTotalGanancia(0);
            $comprobante->setMovimiento('Compra');
            $comprobante->setActivo(1);

            if (is_null($comprobante->getObservaciones())) {
                $comprobante->setObservaciones('');
            }

            $comprobante->setCreatedBy($this->getUser()->getId());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser()->getId());
            $comprobante->setUpdatedAt(new \DateTime("now"));

            $em->persist($comprobante);

            $comprobantedetalle = new ComprobanteDetalle();
            $comprobantedetalles  = $comprobante->getComprobanteDetalles()->toArray();

            foreach($comprobantedetalles as $comprobantedetalle) {
                $comprobantedetalle->setPrecioNeto(0);
                $comprobantedetalle->setImporteGanancia(0);
                $comprobantedetalle->setTotalNoGravado(0);
                $comprobantedetalle->setImporteIvaExento(0);

                if (is_null($comprobantedetalle->getObservaciones())) {
                    $comprobantedetalle->setObservaciones('');
                }
                
                $comprobantedetalle->setTotalNeto($comprobantedetalle->getPrecioCosto()*$comprobantedetalle->getCantidad());
                
                $comprobantedetalle->setComprobante($comprobante);
                $comprobantedetalle->setMovimiento('Compra');
                $comprobantedetalle->setActivo(1);
                $comprobantedetalle->setCreatedBy($this->getUser()->getId());
                $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                $comprobantedetalle->setUpdatedBy($this->getUser()->getId());
                $comprobantedetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($comprobantedetalle);

                //Actualizo datos en el artículo
                $articulo = $comprobantedetalle->getArticulo();
                $articulo->setPrecioCosto($comprobantedetalle->getPrecioCosto());
                $articulo->setGananciaPorcentaje($comprobantedetalle->getPorcentajeGanancia());
                $articulo->setPrecioVenta($comprobantedetalle->getPrecioVenta());
                $articulo->setUltimoComprobante($comprobante);
            }

            $em->flush();
            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobantecompra/new.html.twig', array(
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
        $comprobantedetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobantecompra/show.html.twig', array(
            'comprobante' => $comprobante,
            'comprobantedetalles' => $comprobantedetalles,
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

        $comprobantedetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante, 'activo' => 1));

        if (is_null($comprobante->getObservaciones())) {
            $comprobante->setObservaciones('');
        }

        foreach($comprobantedetalles as $comprobantedetalle) {
            $comprobante->getComprobanteDetalles()->add($comprobantedetalle);
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
            $comprobantedetalleDelete = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findBy(array('comprobante' => $comprobante));

            foreach ($comprobantedetalleDelete as $comprobantedetalle) {
                $comprobantedetalle->setActivo(0);
            }   
            //**********************************************************************

            foreach($editForm->getData()->getComprobanteDetalles() as $comprobantedetalle) {
                $comprobantedetalle->setImporteGanancia(0);
                $comprobantedetalle->setTotalNoGravado(0);
                $comprobantedetalle->setImporteIvaExento(0);

                if (is_null($comprobantedetalle->getObservaciones())) {
                    $comprobantedetalle->setObservaciones('');
                }

                $comprobantedetalle->setTotalNeto($comprobantedetalle->getPrecioCosto()*$comprobantedetalle->getCantidad());
                
                $comprobantedetalle->setComprobante($comprobante);
                $comprobantedetalle->setMovimiento('Compra');
                $comprobantedetalle->setActivo(1);
                $comprobantedetalle->setCreatedBy($this->getUser()->getId());
                $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                $comprobantedetalle->setUpdatedBy($this->getUser()->getId());
                $comprobantedetalle->setUpdatedAt(new \DateTime("now"));

                if (is_null($comprobantedetalle->getId())){     
                    if (is_null($comprobantedetalle->getObservaciones())) {
                        $comprobantedetalle->setObservaciones('');
                    }

                    $comprobantedetalle->setCreatedBy($this->getUser()->getId());
                    $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                    $em->persist($comprobantedetalle);
                }

                //Actualizo datos en el artículo, solo si corresponde al último ingreso del artículo
                $articulo = $comprobantedetalle->getArticulo();
                if (!is_null($articulo->getUltimoComprobante())) {
                    if ($articulo->getUltimoComprobante()->getId() == $comprobante->getId()) {
                        $articulo->setPrecioCosto($comprobantedetalle->getPrecioCosto());
                        $articulo->setGananciaPorcentaje($comprobantedetalle->getPorcentajeGanancia());
                        $articulo->setPrecioVenta($comprobantedetalle->getPrecioVenta());
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('comprobantecompra_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobantecompra/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comprobante entity.
     *
     */
    public function deleteAction(Request $request, Comprobante $comprobante)
    {
        $form = $this->createDeleteForm($comprobante);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comprobante);
            $em->flush();
        }

        return $this->redirectToRoute('comprobantecompra_index');
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
            ->setAction($this->generateUrl('comprobantecompra_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
