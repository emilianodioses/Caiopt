<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comprobante;
use AppBundle\Entity\ComprobanteDetalle;
use AppBundle\Entity\OrdenTrabajo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ComprobanteType;
use AppBundle\Form\ComprobanteDetalleType;
use AppBundle\Form\OrdenTrabajoType;
use Doctrine\Common\Collections\ArrayCollection;

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

        $comprobantes = $em->getRepository('AppBundle:Comprobante')->findBy(Array('movimiento' => 'venta', 'activo'=> '1'));

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
        $form = $this->createForm(ComprobanteType::class, $comprobante);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();

            $max_numero_comprobante = $em->createQueryBuilder()
             ->select('MAX(c.numero)')
             ->from('AppBundle:Comprobante', 'c')
             ->where('c.movimiento = :venta')
             ->setParameter('venta', 'venta')
             ->getQuery()
             ->getSingleScalarResult();

            $comprobante->setNumero($max_numero_comprobante+1);
            $comprobante->setMovimiento('Venta');
            $comprobante->setActivo(1);
            $comprobante->setCreatedBy($this->getUser()->getId());
            $comprobante->setCreatedAt(new \DateTime("now"));
            $comprobante->setUpdatedBy($this->getUser()->getId());
            $comprobante->setUpdatedAt(new \DateTime("now"));

            $em->persist($comprobante);

            $articulo = new ComprobanteDetalle();
            $articulos  = $comprobante->getArticulos()->toArray();

            foreach($articulos as $articulo):  
                $articuloBD = $em->getRepository('AppBundle:Articulo')->find($articulo->getArticulo());

                $comprobantedetaleBD = $em->getRepository('AppBundle:ComprobanteDetalle')
                ->findOneBy(array('articulo' => $articuloBD));

                $articulo->setPrecioCosto($articuloBD->getPrecioCosto());
                $articulo->setPrecioUnitario($comprobantedetaleBD->getPrecioUnitario());
                $articulo->setTotalNeto(($articulo->getPrecioVenta()-$articulo->getBonificacion())*$articulo->getCantidad());
                $articulo->setGanancia(0);;
                $articulo->setImporteGanancia($articulo->getPrecioVenta()-$articulo->getPrecioUnitario());

                $articulo->setMovimiento('Venta');
                $articulo->setComprobante($comprobante);
                $articulo->setActivo(1);
                $articulo->setCreatedBy($this->getUser()->getId());
                $articulo->setCreatedAt(new \DateTime("now"));
                $articulo->setUpdatedBy($this->getUser()->getId());
                $articulo->setUpdatedAt(new \DateTime("now"));

                $em->persist($articulo);

                if ($articuloBD->getOrdenTrabajo()) {
                    $ordenTrabajo = new OrdenTrabajo();

                    //ESTO ESTA MAL, solo lo puse para poner un valor default de taller de estado pendiente
                    $taller = $em->getRepository('AppBundle:Taller')->find(1);
                    $ordenTrabajo->setTaller($taller);

                    $ordenTrabajo->setCliente($comprobante->getCliente());
                    $ordenTrabajo->setComprobante($comprobante);
                    $ordenTrabajo->setEstado('Pendiente');
                                        
                    $ordenTrabajo->setOjoDerechoEje(0);
                    $ordenTrabajo->setOjoDerechoCilindro(0);
                    $ordenTrabajo->setOjoDerechoEsfera(0);
                    $ordenTrabajo->setOjoDerechoAdicc(0);
                    $ordenTrabajo->setOjoDerechoDnp(0);
                    $ordenTrabajo->setOjoDerechoAlt(0);
                    $ordenTrabajo->setOjoIzquierdoEje(0);
                    $ordenTrabajo->setOjoIzquierdoCilindro(0);
                    $ordenTrabajo->setOjoIzquierdoEsfera(0);
                    $ordenTrabajo->setOjoIzquierdoAdicc(0);
                    $ordenTrabajo->setOjoIzquierdoDnp(0);
                    $ordenTrabajo->setOjoIzquierdoAlt(0);
                    $ordenTrabajo->setDip(0);

                    $ordenTrabajo->setActivo(1);
                    $ordenTrabajo->setCreatedBy($this->getUser()->getId());
                    $ordenTrabajo->setCreatedAt(new \DateTime("now"));
                    $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
                    $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

                    $em->persist($ordenTrabajo);
                }
             
            endforeach;    

            $em->flush();
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
        $em = $this->getDoctrine()->getManager();
        $comprobantedetalles = $em->getRepository('AppBundle:ComprobanteDetalle')->findBy(Array('comprobante'=>$comprobante,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($comprobante);

        return $this->render('comprobanteventa/show.html.twig', array(
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

        foreach($comprobantedetalles as $comprobantedetalle) {
            $comprobante->getArticulos()->add($comprobantedetalle);
        }

        $deleteForm = $this->createDeleteForm($comprobante);
        $editForm = $this->createForm(ComprobanteType::class, $comprobante);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

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
                             

            foreach($editForm->getData()->getArticulos() as $comprobantedetalle) {
                $articuloBD = $em->getRepository('AppBundle:Articulo')->find($comprobantedetalle->getArticulo());

                $comprobantedetaleBD = $em->getRepository('AppBundle:ComprobanteDetalle')
                    ->findOneBy(array('articulo' => $articuloBD));

                $comprobantedetalle->setMovimiento('Venta');
                $comprobantedetalle->setUpdatedBy($this->getUser()->getId());
                $comprobantedetalle->setUpdatedAt(new \DateTime("now"));
                $comprobantedetalle->setComprobante($comprobante);
                $comprobantedetalle->setActivo(1);
                $comprobantedetalle->setTotalNeto(($comprobantedetalle->getPrecioVenta()-$comprobantedetalle->getBonificacion())*$comprobantedetalle->getCantidad());
                $comprobantedetalle->setPrecioCosto($articuloBD->getPrecioCosto());
                $comprobantedetalle->setPrecioUnitario($comprobantedetaleBD->getPrecioUnitario());
                $comprobantedetalle->setGanancia(0);;
                $comprobantedetalle->setImporteGanancia($comprobantedetalle->getPrecioVenta()-$comprobantedetalle->getPrecioUnitario());

                if (is_null($comprobantedetalle->getId())){     
                    $comprobantedetalle->setCreatedBy($this->getUser()->getId());
                    $comprobantedetalle->setCreatedAt(new \DateTime("now"));
                    $em->persist($comprobantedetalle);
                }

            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('comprobanteventa_show', array('id' => $comprobante->getId()));
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
