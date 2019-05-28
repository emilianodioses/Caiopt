<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajoContactologia;
use AppBundle\Entity\OrdenTrabajoContactologiaDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\OrdenTrabajoContactologiaType;
use Symfony\Component\HttpFoundation\Response;


/**
 * Ordentrabajocontactologia controller.
 *
 */
class OrdenTrabajoContactologiaController extends Controller
{
    /**
     * Lists all ordenTrabajoContactologia entities.
     *
     */
    public function indexAction()
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajoContactologia', 'Index', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenesTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->findBy(array('activo'=>1, 'sucursal' => $this->getUser()->getSucursal()));

        return $this->render('ordentrabajocontactologia/index.html.twig', array(
            'ordenesTrabajoContactologia' => $ordenesTrabajoContactologia,
        ));
    }

    /**
     * Creates a new ordenTrabajoContactologia entity.
     *
     */
    public function newAction(Request $request)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $ordenTrabajoContactologia = new Ordentrabajocontactologia();
        $form = $this->createForm('AppBundle\Form\OrdenTrabajoContactologiaType', $ordenTrabajoContactologia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());       
        
            $ordenTrabajoContactologia->setSucursal($sucursal);
            $ordenTrabajoContactologia->setActivo(1);
            $ordenTrabajoContactologia->setCreatedBy($this->getUser()->getId());
            $ordenTrabajoContactologia->setCreatedAt(new \DateTime("now"));
            $ordenTrabajoContactologia->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajoContactologia->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenTrabajoContactologia);

            $ordentrabajocontactologiadetalle = new OrdenTrabajoContactologiaDetalle();
            $ordentrabajocontactologiadetalles  = $ordenTrabajoContactologia->getOrdenTrabajoContactologiaDetalles()->toArray();

            foreach($ordentrabajocontactologiadetalles as $ordentrabajocontactologiadetalle) {
                $ordentrabajocontactologiadetalle->setOrdenTrabajoContactologia($ordenTrabajoContactologia);
                $ordentrabajocontactologiadetalle->setActivo(1);
                $ordentrabajocontactologiadetalle->setCreatedBy($this->getUser()->getId());
                $ordentrabajocontactologiadetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajocontactologiadetalle->setUpdatedBy($this->getUser()->getId());
                $ordentrabajocontactologiadetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajocontactologiadetalle);
            }

            $em->flush();

            return $this->redirectToRoute('ordentrabajocontactologia_show', array('id' => $ordenTrabajoContactologia->getId()));
        }

        return $this->render('ordentrabajocontactologia/new.html.twig', array(
            'ordenTrabajoContactologia' => $ordenTrabajoContactologia,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ordenTrabajoContactologia entity.
     *
     */
    public function showAction(OrdenTrabajoContactologia $ordenTrabajoContactologia)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'Show', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();
        $ordentrabajocontactologiadetalles = $em->getRepository('AppBundle:OrdenTrabajoContactologiaDetalle')->findBy(Array('ordenTrabajoContactologia'=>$ordenTrabajoContactologia,  'activo'=>1));

        $deleteForm = $this->createDeleteForm($ordenTrabajoContactologia);

        return $this->render('ordentrabajocontactologia/show.html.twig', array(
            'ordenTrabajoContactologia' => $ordenTrabajoContactologia,
            'ordentrabajocontactologiadetalles' => $ordentrabajocontactologiadetalles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Cerrar orden de trabajo
     *
     */
    public function cerrarAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'Cerrar', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($id);
        $ordenTrabajoContactologia->setEstado("Finalizado");  

        $ordenTrabajoContactologia->setUpdatedBy($this->getUser()->getId()); 
        $ordenTrabajoContactologia->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($ordenTrabajoContactologia);
        
        return $this->redirectToRoute('ordentrabajocontactologia_show', array('id' => $ordenTrabajoContactologia->getId()));
    }

    /**
     * Displays a form to edit an existing ordenTrabajoContactologia entity.
     *
     */
    public function editAction(Request $request, OrdenTrabajoContactologia $ordenTrabajoContactologia)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'Edit', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordentrabajocontactologiadetalles = $em->getRepository('AppBundle:OrdenTrabajoContactologiaDetalle')->findBy(Array('ordenTrabajoContactologia'=>$ordenTrabajoContactologia,  'activo'=>1));

        foreach($ordentrabajocontactologiadetalles as $ordentrabajocontactologiadetalle) {
            $ordenTrabajoContactologia->getOrdenTrabajoContactologiaDetalles()->add($ordentrabajocontactologiadetalle);
        }

        $deleteForm = $this->createDeleteForm($ordenTrabajoContactologia);
        $editForm = $this->createForm(OrdenTrabajoContactologiaType::class, $ordenTrabajoContactologia);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ordenTrabajoContactologia->setUpdatedBy($this->getUser()->getId());
            $ordenTrabajoContactologia->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenTrabajoContactologia);

            $ordentrabajocontactologiadetalle = new OrdenTrabajoContactologiaDetalle();
            $ordentrabajocontactologiadetalles  = $ordenTrabajoContactologia->getOrdenTrabajoContactologiaDetalles()->toArray();

            foreach($ordentrabajocontactologiadetalles as $ordentrabajocontactologiadetalle) {
                $ordentrabajocontactologiadetalle->setOrdenTrabajo($ordenTrabajoContactologia);
                $ordentrabajocontactologiadetalle->setActivo(1);
                $ordentrabajocontactologiadetalle->setCreatedBy($this->getUser()->getId());
                $ordentrabajocontactologiadetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajocontactologiadetalle->setUpdatedBy($this->getUser()->getId());
                $ordentrabajocontactologiadetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajocontactologiadetalle);
            }

            $em->flush();

            return $this->redirectToRoute('ordentrabajocontactologia_show', array('id' => $ordenTrabajoContactologia->getId()));
        }

        return $this->render('ordentrabajocontactologia/edit.html.twig', array(
            'ordenTrabajoContactologia' => $ordenTrabajoContactologia,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ordenTrabajoContactologia entity.
     *
     */
    public function deleteAction($id)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'Delete', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($id);
        if ($ordenTrabajoContactologia->getActivo() > 0)
            $ordenTrabajoContactologia->setActivo(0);
        else
            $ordenTrabajoContactologia->setActivo(1);  

        $ordenTrabajoContactologia->setUpdatedBy($this->getUser()->getId()); 
        $ordenTrabajoContactologia->setUpdatedAt(new \DateTime("now")); 
        
        $em->flush($ordenTrabajoContactologia);
        
        return $this->redirectToRoute('ordentrabajocontactologia_index');
    }

    /**
     * Creates a form to delete a ordenTrabajoContactologia entity.
     *
     * @param OrdenTrabajoContactologia $ordenTrabajoContactologia The ordenTrabajoContactologia entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrdenTrabajoContactologia $ordenTrabajoContactologia)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ordentrabajocontactologia_delete', array('id' => $ordenTrabajoContactologia->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Imprime la orden de trabajo
     *
     */
    public function ordenImprimirAction(Request $request, OrdentrabajoContactologia $ordenTrabajoContactologia)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajOcontactologia', 'Imprimir', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenTrabajoContactologia = $em->getRepository('AppBundle:OrdenTrabajoContactologia')->find($ordenTrabajoContactologia);

        $ordentrabajocontactologiadetalles = $em->getRepository('AppBundle:OrdenTrabajoContactologiaDetalle')->findBy(Array('ordenTrabajoContactologia'=>$ordenTrabajoContactologia,  'activo'=>1));

        $ordenTemplate = 'ordentrabajocontactologia/orden_imprimir.html.twig';

        $html = $this->renderView($ordenTemplate, array(
            'ordentrabajocontactologiadetalles' => $ordentrabajocontactologiadetalles,
            'ordenTrabajoContactologia' => $ordenTrabajoContactologia,
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
