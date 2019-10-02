<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenTrabajo;
use AppBundle\Entity\OrdenTrabajoDetalle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\OrdenTrabajoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;


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
        
        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->findBy(array('activo'=>1, 'sucursal' => $this->getUser()->getSucursal()), array('id' => 'DESC'));

        return $this->render('ordentrabajo/index.html.twig', array(
            'ordenesTrabajo' => $ordenesTrabajo,
        ));
    }

    /**
     * Creates a new ordenTrabajo entity.
     *
     */
    public function newAction(Request $request, $clienteId)
    {
        // Permisos de Usuario para Acciones
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'New', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;

        $em = $this->getDoctrine()->getManager();

        $ordenTrabajo = new Ordentrabajo();
        
        if ($clienteId > 0) {
            $cliente = $em->getRepository('AppBundle:Cliente')->find($clienteId);
            $ordenTrabajo->setCliente($cliente);
            if (!is_null($cliente->getObraSocialPlan())) {
                 $ordenTrabajo->setObraSocialPlan($cliente->getObraSocialPlan());
            }
        }

        $ordenTrabajo->setFechaRecepcion(new \DateTime("now"));
        $ordenTrabajo->setUsuario($this->getUser());
        $form = $this->createForm(OrdenTrabajoType::class, $ordenTrabajo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sucursal = $em->getRepository('AppBundle:Sucursal')->find($this->getUser()->getSucursal()->getId());
        
            $ordenTrabajo->setSucursal($sucursal);
            $ordenTrabajo->setActivo(1);
            $ordenTrabajo->setCreatedBy($this->getUser());
            $ordenTrabajo->setCreatedAt(new \DateTime("now"));
            $ordenTrabajo->setUpdatedBy($this->getUser());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

            $em->persist($ordenTrabajo);

            $ordentrabajodetalle = new OrdenTrabajoDetalle();
            $ordentrabajodetalles  = $ordenTrabajo->getOrdenTrabajoDetalles()->toArray();

            foreach($ordentrabajodetalles as $ordentrabajodetalle) {
                $ordentrabajodetalle->setOrdenTrabajo($ordenTrabajo);
                $ordentrabajodetalle->setActivo(1);
                $ordentrabajodetalle->setCreatedBy($this->getUser());
                $ordentrabajodetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajodetalle->setUpdatedBy($this->getUser());
                $ordentrabajodetalle->setUpdatedAt(new \DateTime("now"));

                $em->persist($ordentrabajodetalle);
            }

            $cliente = $ordenTrabajo->getCliente();
            $cliente->setObraSocialPlan($ordenTrabajo->getObraSocialPlan());
            $cliente->setUpdatedBy($this->getUser());
            $cliente->setUpdatedAt(new \DateTime("now"));

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

            $ordenTrabajo->setUpdatedBy($this->getUser());
            $ordenTrabajo->setUpdatedAt(new \DateTime("now"));

            $ordentrabajodetalle = new OrdenTrabajoDetalle();
            $ordentrabajodetalles  = $ordenTrabajo->getOrdenTrabajoDetalles()->toArray();

            foreach($ordentrabajodetalles as $ordentrabajodetalle) {
                $ordentrabajodetalle->setOrdenTrabajo($ordenTrabajo);
                $ordentrabajodetalle->setActivo(1);
                $ordentrabajodetalle->setCreatedBy($this->getUser());
                $ordentrabajodetalle->setCreatedAt(new \DateTime("now"));
                $ordentrabajodetalle->setUpdatedBy($this->getUser());
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

        $ordenTrabajo->setUpdatedBy($this->getUser()); 
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

        $ordenTrabajo->setUpdatedBy($this->getUser()); 
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

    public function findAction(Request $req) {

        $em = $this->getDoctrine()->getManager();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        // Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, array('json' => new JsonEncoder()));
        
        $cliente = $em->getRepository('AppBundle:Cliente')->find($req->get('clienteId'));

        $query = $em->createQuery('SELECT MAX(o.id) FROM AppBundle:OrdenTrabajo o WHERE o.cliente = :cliente');
        $query->setParameter('cliente', $cliente);


        if ($query->getSingleScalarResult() != null)
            $ordenTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($query->getSingleScalarResult());
        else
            $ordenTrabajo = "";
        
        $j_ordenTrabajo = $serializer->serialize($ordenTrabajo, 'json');

        return JsonResponse::create(array('ordenTrabajo' => $j_ordenTrabajo));
    }

    /**
     * Imprime la orden de trabajo
     *
     */
    public function ordenImprimirAction(Request $request, Ordentrabajo $ordenTrabajo)
    {
        // Permisos de Usuario para Acciones
        /*
        $secure = $this->container->get('SecureAction');
        
        if (!$secure->isAuthorized('OrdenTrabajo', 'OrdenImprimir', $this->getUser()->getRol())):
            return new Response('Acceso denegado. Por favor solicite acceso al administrador de sistema.');
        endif;
        */
        $em = $this->getDoctrine()->getManager();

        $ordenesTrabajo = $em->getRepository('AppBundle:OrdenTrabajo')->find($ordenTrabajo);

        $ordentrabajodetalles = $em->getRepository('AppBundle:OrdenTrabajoDetalle')->findBy(Array('ordenTrabajo'=>$ordenTrabajo,  'activo'=>1));

        $ordenTemplate = 'ordentrabajo/orden_imprimir.html.twig';

        $html = $this->renderView($ordenTemplate, array(
            'ordentrabajodetalles' => $ordentrabajodetalles,
            'ordenTrabajo' => $ordenesTrabajo,
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
