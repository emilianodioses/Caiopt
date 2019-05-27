<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajo;
use AppBundle\Entity\OrdenTrabajoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\OrdenTrabajoType;
use Symfony\Component\HttpFoundation\Response;


/**
 * Ordentrabajo controller.
 *
 */
class OrdenTrabajoController extends Controller
{
    /**
     * Lists all ordenTrabajo entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        
        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findBy(array('activo'=>1));

        return $this->render('ordentrabajo/index.html.twig', array(
            'ordenesTrabajo' => $ordenesTrabajo,
        ));
    }

    /**
     * Creates a new ordenTrabajo entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $ordenTrabajo = new Ordentrabajo();
        $ordenTrabajo->setFechaRecepcion(new \DateTime("now"));
        $form = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
        
            $ordenTrabajo->setSucursal($sucursal);
            $ordenTrabajo->setActivo(1);
            $ordenTrabajo->setCreatedBy($this->getUser()->getId());
            $ordenTrabajo->setCreatedAt(new \DateTime("now"));
            $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenTrabajo);

            $ordentrabajodetalle = new OrdenTrabajoDetalle();
            $ordentrabajodetalles  = $ordenTrabajo->getOrdenTrabajoDetalles()->toArray();

            foreach($ordentrabajodetalles as $ordentrabajodetalle) {
                $ordentrabajodetalle->setOrdenTrabajo($ordenTrabajo);
                $ordentrabajodetalle->setActivo(1);
                $ordentrabajodetalle->setCreatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajodetalle->setUpdatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajodetalle);
            }

            $em->flush();

            return $this->redirectToRoute('ordentrabajo_show', array('id' => $ordenTrabajo->getId()));
        }

        return $this->render('ordentrabajo/new.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ordenTrabajo entity.
     *
     */
    public function showAction(OrdenTrabajo $ordenTrabajo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($ordenTrabajo);

        return $this->render('ordentrabajo/show.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'ordentrabajodetalles' => $ordentrabajodetalles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ordenTrabajo entity.
     *
     */
    public function editAction(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        foreach($ordentrabajodetalles as $ordentrabajodetalle) {
            $ordenTrabajo->getOrdenTrabajoDetalles()->add($ordentrabajodetalle);
        }

        $deleteForm = $this->createDeleteForm($ordenTrabajo);
        $editForm = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ordenTrabajo->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

            $ordentrabajodetalle = new OrdenTrabajoDetalle();
            $ordentrabajodetalles  = $ordenTrabajo->getOrdenTrabajoDetalles()->toArray();

            foreach($ordentrabajodetalles as $ordentrabajodetalle) {
                $ordentrabajodetalle->setOrdenTrabajo($ordenTrabajo);
                $ordentrabajodetalle->setActivo(1);
                $ordentrabajodetalle->setCreatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajodetalle->setUpdatedBy($this->getUser()->getId());
                $ordentrabajodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajodetalle);
            }

            $em->flush();

            return $this->redirectToRoute('ordentrabajo_show', array('id' => $ordenTrabajo->getId()));
        }

        return $this->render('ordentrabajo/edit.html.twig', array(
            'ordenTrabajo' => $ordenTrabajo,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ordenTrabajo entity.
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

        $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($id);
        if ($ordenTrabajo->getActivo() > 0)
            $ordenTrabajo->setActivo(0);
        else
            $ordenTrabajo->setActivo(1);  

        $ordenTrabajo->setUpdatedBy($this->getUser()->getId()); 
        $ordenTrabajo->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($ordenTrabajo);
        
        return $this->redirectToRoute('ordentrabajo_index');
    }

    /**
     * Cerrar orden de trabajo
     *
     */
    public function cerrarAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'Cerrar', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($id);
        $ordenTrabajo->setEstado("Finalizado");  

        $ordenTrabajo->setUpdatedBy($this->getUser()->getId()); 
        $ordenTrabajo->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($ordenTrabajo);
        
        return $this->redirectToRoute('ordentrabajo_show', array('id' => $ordenTrabajo->getId()));
    }

    /**
     * Creates a form to delete a ordenTrabajo entity.
     *
     * @param OrdenTrabajo $ordenTrabajo The ordenTrabajo entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenTrabajo $ordenTrabajo)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordentrabajo_delete', array('id' => $ordenTrabajo->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Imprime la orden de trabajo
     *
     */
    public function ordenImprimirAction(Request $request, Ordentrabajo $ordenTrabajo)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'Imprimir', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($ordenTrabajo);

        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        $ordenTemplate = 'ordentrabajo/orden_imprimir.html.twig';

        $html = $this->renderView($ordenTemplate, array(
            'ordentrabajodetalles' => $ordentrabajodetalles,
            'ordenTrabajo' => $ordenesTrabajo,
            'empresaDireccion' => $this->container->getParameter('empresa_direccion'),
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
        //$pdf->SetAuthor('Our Code World');
        //$pdf->SetTitle(('Our Code World Title'));
        //$pdf->SetSubject('Our Code World Subject');
        //$pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        
        $filename = 'test';//$ordenTrabajo->getNumero();
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
    }
}
