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
class ComprobanteVentaController extends Controller
{
    /**
     * Lists all comprobante entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta'));

        return $this->render('comprobanteventa/index.html.twig', array(
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
        $form = $this->createForm(ComprobanteType::Class, $comprobante);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            //echo '<pre>';
            //var_export($form->getData());
            //die;

            $em = $this->getDoctrine()->getManager();

            $max_numero_comprobante = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:Comprobante', 'c')
             ->where('c.movimiento = :venta')
             ->setParameter('venta', 'venta')
             ->getQuery()
             ->getSingleScalarResult();
        
            //$comprobante->setTotalGanancia(1);
            //$comprobante->setTotalCosto(1);
            //$comprobante->setImporteTributos(0);

            $comprobante->setNumero($max_numero_comprobante+1);
            $comprobante->setMovimiento('Venta');
            $comprobante->setActivo(1);
            $comprobante->setCreatedBy($this->getUser()->getId());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser()->getId());
            $comprobante->setUpdatedAt(new \DateTime("now"));

            $em->persist($comprobante);
            $em->flush();

            $articulo = new ComprobanteDetalle();
            $articulos  = $comprobante->getArticulos()->toArray();

            foreach($articulos as $articulo):  

                /*
                $precio_costo = $em->createQueryBuilder()
                 ->select('c.precioCosto')
                 ->from('AppBundle:ComprobanteDetalle', 'c')
                 ->getQuery()
                 ->getSingleScalarResult();
                */

                $articuloBD = $em->getRepository('AppBundle:Articulo')->find($articulo->getArticulo());
                //$articulo->setPrecioCosto($precio_costo-($precio_costo*(1+$articulo->getBonificacion()/100)));
                $articulo->setPrecioCosto($articuloBD->getPrecioCosto()-($articuloBD->getPrecioCosto()*(1+$articulo->getBonificacion()/100)));
                $articulo->setPrecioUnitario($articuloBD->getPrecioCosto());
                //$articulo->setPrecioUnitario($precio_costo);
                //precioVenta x cantidad - Bonificacion
                $articulo->setTotalNeto(($articulo->getPrecioVenta()-$articulo->getBonificacion())*$articulo->getCantidad());
                $articulo->setGanancia(0);;

                $articulo->setImporteGanancia($articulo->getPrecioVenta()-$articulo->getPrecioUnitario());

                $articulo->setComprobante($comprobante);
                $articulo->setActivo(1);
                $articulo->setCreatedBy($this->getUser()->getId());
                $articulo->setCreatedAt(new \DateTime("now"));
                $articulo->setUpdatedBy($this->getUser()->getId());
                $articulo->setUpdatedAt(new \DateTime("now"));

                $em->persist($articulo);
                $em->flush(); 
            endforeach;    

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/new.html.twig', array(
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
        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobanteventa/show.html.twig', array(
            'comprobante' => $comprobante,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comprobante entity.
     *
     */
    public function editAction(Request $request, Comprobante $comprobante)
    {
        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comprobanteventa_edit', array('id' => $comprobante->getId()));
        }

        return $this->render('comprobanteventa/edit.html.twig', array(
            'comprobante' => $comprobante,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comprobante entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $comprobante = $em->getRepository('AppBundle:Comprobante')->find($id);
        if ($comprobante->getActivo() > 0)
            $comprobante->setActivo(0);
        else
            $comprobante->setActivo(1);  

        $comprobante->setUpdatedBy($this->getUser()->getId()); 
        $comprobante->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($comprobante);
        
        return $this->redirectToRoute('comprobanteventa_index');
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
            ->setAction($this->generateUrl('comprobanteventa_delete', array('id' => $comprobante->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
