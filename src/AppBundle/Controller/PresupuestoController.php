<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Presupuesto;
use AppBundle\Entity\PresupuestoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\PresupuestoType;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\PresupuestoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


/**
 * Presupuesto controller.
 *
 */
class PresupuestoController extends Controller
{
    /**
     * Lists all presupuesto entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:Presupuesto')->findByTexto($texto);
        $presupuestos = $query->getResult();



        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $presupuestos,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('presupuesto/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }
    /**
     * Creates a new presupuesto entity.
     *
     */
    public function newAction(Request $request, $clienteId)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        // Obtengo sucursal actual
        $sucuralActual = $this->getUser()->getSucursal();

        if (!$secure->isAuthorized('OrdenTrabajo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $presupuesto = new Presupuesto();

        if ($clienteId > 0) {
            $cliente = $em->getRepository('AppBundle:Cliente')->find($clienteId);
            $presupuesto->setCliente($cliente);
        }

        $presupuesto->setFechaPresup(new \DateTime("now"));
        $presupuesto->setUsuario($this->getUser());
        $presupuesto->setRetiro($sucuralActual);
        $form = $this->createForm(PresupuestoType::class, $presupuesto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $presupuesto->setActivo(1);
            $presupuesto->setCreatedBy($this->getUser());
            $presupuesto->setCreatedAt(new \DateTime("now"));
            $presupuesto->setUpdatedBy($this->getUser());
            $presupuesto->setUpdatedAt(new \DateTime("now"));

            $em->persist($presupuesto);

            $presupuestodetalle = new PresupuestoDetalle();
            $presupuestodetalles  = $presupuesto->getPresupuestoDetalles()->toArray();

            foreach($presupuestodetalles as $presupuestodetalle) {
                $presupuestodetalle->setPresupuesto($presupuesto);
                $presupuestodetalle->setActivo(1);
                $presupuestodetalle->setCreatedBy($this->getUser());
                $presupuestodetalle->setCreatedAt(new \DateTime("now"));
                $presupuestodetalle->setUpdatedBy($this->getUser());
                $presupuestodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($presupuestodetalle);
            }

            $cliente = $presupuesto->getCliente();
            $cliente->setUpdatedBy($this->getUser());
            $cliente->setUpdatedAt(new \DateTime("now"));

            $em->flush();

            return $this->redirectToRoute('presupuesto_show', array('id' => $presupuesto->getId()));
        }

        return $this->render('presupuesto/new.html.twig', array(
            'presupuesto' => $presupuesto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a presupuesto entity.
     *
     */
    public function showAction(Presupuesto $presupuesto)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

//        if (!$secure->isAuthorized('Presupuesto', 'Show', $this->getUser()->getRol())):
//            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
//        endif;

        $em = $this->getDoctrine()->getManager();
        $presupuestodetalles = $em->getRepository('AppBundle:PresupuestoDetalle')->findBy(Array('presupuesto'=>$presupuesto,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($presupuesto);

        return $this->render('presupuesto/show.html.twig', array(
            'presupuesto' => $presupuesto,
            'presupuestodetalles' => $presupuestodetalles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing presupuesto entity.
     *
     */
    public function editAction(Request $request, Presupuesto $presupuesto)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('OrdenTrabajo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        //echo $presupuesto->getComprobante()->getId();
        //die;

        $em = $this->getDoctrine()->getManager();

        $presupuestodetalles = $em->getRepository('AppBundle:PresupuestoDetalle')->findBy(Array('presupuesto'=>$presupuesto,  'activo'=>1));

        foreach($presupuestodetalles as $presupuestodetalle) {
            $presupuesto->getPresupuestoDetalles()->add($presupuestodetalle);
        }

        $deleteForm = $this->createDeleteForm($presupuesto);
        $editForm = $this->createForm(PresupuestoType::class, $presupuesto);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $presupuesto->setUpdatedBy($this->getUser());
            $presupuesto->setUpdatedAt(new \DateTime("now"));

            $presupuestodetalle = new PresupuestoDetalle();
            $presupuestodetalles  = $presupuesto->getPresupuestoDetalles()->toArray();

            foreach($presupuestodetalles as $presupuestodetalle) {
                $presupuestodetalle->setPresupuesto($presupuesto);
                $presupuestodetalle->setActivo(1);
                $presupuestodetalle->setCreatedBy($this->getUser());
                $presupuestodetalle->setCreatedAt(new \DateTime("now"));
                $presupuestodetalle->setUpdatedBy($this->getUser());
                $presupuestodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($presupuestodetalle);
            }

            $em->flush();

            return $this->redirectToRoute('presupuesto_show', array('id' => $presupuesto->getId()));
        }

        return $this->render('presupuesto/edit.html.twig', array(
            'presupuesto' => $presupuesto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a presupuesto entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');

        if (!$secure->isAuthorized('OrdenTrabajo', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $presupuesto = $em->getRepository('AppBundle:Presupuesto')->find($id);
        if ($presupuesto->getActivo() > 0)
            $presupuesto->setActivo(0);
        else
            $presupuesto->setActivo(1);

        $presupuesto->setUpdatedBy($this->getUser());
        $presupuesto->setUpdatedAt(new \DateTime("now"));

        $em->flush();

        return $this->redirectToRoute('presupuesto_index');
    }
    /**
     * Creates a form to delete a presupuesto entity.
     *
     * @param Presupuesto $presupuesto The presupuesto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Presupuesto $presupuesto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('presupuesto_delete', array('id' => $presupuesto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    /**
     * Imprime presupuesto
     *
     */
    public function PresupuestoImprimirAction(Request $request, Presupuesto $presupuesto)
    {

        $em = $this->getDoctrine()->getManager();

        // Obtengo sucursal actual
        $sucuralActual = $this->getUser()->getSucursal();

        $presupuestos = $em->getRepository('AppBundle:Presupuesto')->find($presupuesto);

        $presupuestodetalles = $em->getRepository('AppBundle:PresupuestoDetalle')->findBy(Array('presupuesto'=>$presupuesto,  'activo'=>1));

        $presupuestoTemplate = 'presupuesto/presupuesto_imprimir.html.twig';

        $iva = (float)$presupuesto->getIva21() / 100;

        $totalIva = (float)$presupuesto->getTotalPresupuesto() - 
                    (float)$presupuesto->getTotalPresupuesto() / (1 + $iva);
        $totalIva = number_format($totalIva, 2, '.', '');

        $html = $this->renderView($presupuestoTemplate, array(
            'presupuestodetalles' => $presupuestodetalles,
            'presupuesto' => $presupuestos,
            'iva' => $totalIva,
            'sucursalActual' => $sucuralActual
        )
        );

        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('helvetica', '', 11, '', true);
       $pdf->AddPage();

        $filename = 'test';//$ordenTrabajo->getNumero();

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }
}
