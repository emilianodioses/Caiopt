<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LibroCaja;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\LibroCajaDetalle;

/**
 * Librocaja controller.
 *
 */
class LibroCajaController extends Controller
{
    /**
     * Lists all libroCaja entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $texto = $request->get('texto','');

        $query = $em->getRepository('AppBundle:LibroCaja')->findByTexto($texto, $this->getUser()->getSucursal()->getId());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1)/*page number*/,
            15/*limit per page*/
        );

        return $this->render('librocaja/index.html.twig', array(
            'pagination' => $pagination,
            'texto' => $texto
        ));
    }

    /**
     * Creates a new libroCaja entity.
     *
     */
    public function newAction(Request $request)
    {
        $libroCaja = new Librocaja();
        $libroCaja->setFecha(new \DateTime("now"));
        $libroCaja->setSaldoFinal(0.00);
        $libroCaja->setCaja(0.00);
        $libroCaja->setDiferencia(0.00);
        $form = $this->createForm('AppBundle\Form\LibroCajaType', $libroCaja);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $libroCaja_existente = $em->getRepository('AppBundle:Librocaja')->findBy(Array('fecha' => $libroCaja->getFecha(), 'activo' => 1, 'sucursal' => $this->getUser()->getSucursal()));

            if (count($libroCaja_existente) > 0) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un libro caja con la fecha ingresada.');
                return $this->render('librocaja/new.html.twig', array(
                    'libroCaja' => $libroCaja,
                    'form' => $form->createView(),
                ));
            }

            $libroCaja->setSaldoFinal($libroCaja->getSaldoInicial());
            $libroCaja->setSucursal($this->getUser()->getSucursal());
            $libroCaja->setActivo(true);
            $libroCaja->setCreatedBy($this->getUser()->getId());
            $libroCaja->setCreatedAt(new \DateTime("now"));
            $libroCaja->setUpdatedBy($this->getUser()->getId());
            $libroCaja->setUpdatedAt(new \DateTime("now"));

            $em->persist($libroCaja);

            //Creo el libroCajaDetalle Inicial
            $libroCajaDetalle = new LibroCajaDetalle();

            $pagoTipo_efectivo = $em->getRepository('AppBundle:PagoTipo')->find(1);
            $categoria_ingreso_sin_especificar = $em->getRepository('AppBundle:MovimientoCategoria')->find(6);

            $libroCajaDetalle->setLibroCaja($libroCaja);
            $libroCajaDetalle->setPagoTipo($pagoTipo_efectivo);
            $libroCajaDetalle->setOrigen('Manual');
            $libroCajaDetalle->setTipo('Ingreso a Caja');
            $libroCajaDetalle->setDescripcion('Inicio de Caja');
            $libroCajaDetalle->setImporte($libroCaja->getSaldoInicial());
            $libroCajaDetalle->setMovimientoCategoria($categoria_ingreso_sin_especificar);
            $libroCajaDetalle->setActivo(true);
            $libroCajaDetalle->setCreatedBy($this->getUser()->getId());
            $libroCajaDetalle->setCreatedAt(new \DateTime("now"));
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));

            $em->persist($libroCajaDetalle);

            $em->flush();

            return $this->redirectToRoute('librocaja_show', array('id' => $libroCaja->getId()));
        }

        return $this->render('librocaja/new.html.twig', array(
            'libroCaja' => $libroCaja,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a libroCaja entity.
     *
     */
    public function showAction(LibroCaja $libroCaja)
    {
        $em = $this->getDoctrine()->getManager();

        $sucursal_id = $this->getUser()->getSucursal()->getId();

        $pagoTipos = $em->getRepository('AppBundle:PagoTipo')->findBy(Array('activo'=> '1'), array('nombre' => 'ASC'));

        //inicializo el arreglo de los tipos de pago
        $ingresos = array();
        $egresos = array();
        foreach($pagoTipos as $pagoTipo) {
            $ingresos[$pagoTipo->getId()]['descripcion'] = $pagoTipo->getNombre();
            $ingresos[$pagoTipo->getId()]['total'] = 0;
            $ingresos[$pagoTipo->getId()]['porcentaje'] = 0;

            $egresos[$pagoTipo->getId()]['descripcion'] = $pagoTipo->getNombre();
            $egresos[$pagoTipo->getId()]['total'] = 0;
            $egresos[$pagoTipo->getId()]['porcentaje'] = 0;
        }

        //Calculo los totales de ingresos por tipos de pago
        $total_ingresos = 0;
        $total_egresos = 0;
        $libroCajaDetalles = $em->getRepository('AppBundle:LibroCajaDetalle')->findBy(Array('libroCaja' => $libroCaja, 'activo' => 1));
        foreach($libroCajaDetalles as $libroCajaDetalle) {
            if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                $ingresos[$libroCajaDetalle->getPagoTipo()->getId()]['total'] += $libroCajaDetalle->getImporte();
                
                $total_ingresos += $libroCajaDetalle->getImporte();
            }
            else {
                $egresos[$libroCajaDetalle->getPagoTipo()->getId()]['total'] += $libroCajaDetalle->getImporte();
                
                $total_egresos += $libroCajaDetalle->getImporte();
            }
        }

        //Calculo los porcentajes de cada condicion de venta, siempre que las haya
        if ($total_ingresos != 0) {
            foreach($pagoTipos as $pagoTipo) {
                $ingresos[$pagoTipo->getId()]['porcentaje'] = number_format($ingresos[$pagoTipo->getId()]['total'] * 100 / $total_ingresos, 2);
            }
        }

        if ($total_egresos != 0) {
            foreach($pagoTipos as $pagoTipo) {
                $egresos[$pagoTipo->getId()]['porcentaje'] = number_format($egresos[$pagoTipo->getId()]['total'] * 100 / $total_egresos, 2);
            }
        }

        return $this->render('librocaja/show.html.twig', array(
            'libroCaja' => $libroCaja,
            'libroCajaDetalles' => $libroCajaDetalles,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
        ));
    }

    /**
     * Displays a form to edit an existing libroCaja entity.
     *
     */
    public function editAction(Request $request, LibroCaja $libroCaja)
    {
        $saldo_inicial_anterior = $libroCaja->getSaldoInicial();

        $libroCaja->setDiferencia($libroCaja->getCaja() - $libroCaja->getSaldoFinal());
        $editForm = $this->createForm('AppBundle\Form\LibroCajaType', $libroCaja);
        $editForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $sucursal_id = $this->getUser()->getSucursal()->getId();

        $pagoTipos = $em->getRepository('AppBundle:PagoTipo')->findBy(Array('activo'=> '1'), array('nombre' => 'ASC'));

        //inicializo el arreglo de los tipos de pago
        $ingresos = array();
        $egresos = array();
        foreach($pagoTipos as $pagoTipo) {
            $ingresos[$pagoTipo->getId()]['descripcion'] = $pagoTipo->getNombre();
            $ingresos[$pagoTipo->getId()]['total'] = 0;
            $ingresos[$pagoTipo->getId()]['porcentaje'] = 0;

            $egresos[$pagoTipo->getId()]['descripcion'] = $pagoTipo->getNombre();
            $egresos[$pagoTipo->getId()]['total'] = 0;
            $egresos[$pagoTipo->getId()]['porcentaje'] = 0;
        }

        //Calculo los totales de ingresos por tipos de pago
        $total_ingresos = 0;
        $total_egresos = 0;
        $libroCajaDetalles = $em->getRepository('AppBundle:LibroCajaDetalle')->findBy(Array('libroCaja' => $libroCaja, 'activo' => 1));
        foreach($libroCajaDetalles as $libroCajaDetalle) {
            if ($libroCajaDetalle->getTipo() == 'Ingreso a Caja') {
                $ingresos[$libroCajaDetalle->getPagoTipo()->getId()]['total'] += $libroCajaDetalle->getImporte();
                
                $total_ingresos += $libroCajaDetalle->getImporte();
            }
            else {
                $egresos[$libroCajaDetalle->getPagoTipo()->getId()]['total'] += $libroCajaDetalle->getImporte();
                
                $total_egresos += $libroCajaDetalle->getImporte();
            }
        }

        //Calculo los porcentajes de cada condicion de venta, siempre que las haya
        if ($total_ingresos != 0) {
            foreach($pagoTipos as $pagoTipo) {
                $ingresos[$pagoTipo->getId()]['porcentaje'] = number_format($ingresos[$pagoTipo->getId()]['total'] * 100 / $total_ingresos, 2);
            }
        }

        if ($total_egresos != 0) {
            foreach($pagoTipos as $pagoTipo) {
                $egresos[$pagoTipo->getId()]['porcentaje'] = number_format($egresos[$pagoTipo->getId()]['total'] * 100 / $total_egresos, 2);
            }
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $libroCaja_existente = $em->getRepository('AppBundle:Librocaja')->findBy(Array('fecha' => $libroCaja->getFecha(), 'activo' => 1));

            if(count($libroCaja_existente) == 1 && $libroCaja_existente[0]->getId() != $libroCaja->getId()) {
                $this->get('session')->getFlashbag()->add('warning', 'Ya existe un libro caja con la fecha ingresada.');
                return $this->render('librocaja/edit.html.twig', array(
                    'libroCaja' => $libroCaja,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

            $saldo = $libroCaja->getSaldoFinal();
            $saldo -= $saldo_inicial_anterior - $libroCaja->getSaldoInicial();

            $libroCaja->setSaldoFinal($saldo);
            $libroCaja->setUpdatedBy($this->getUser()->getId());
            $libroCaja->setUpdatedAt(new \DateTime("now"));

            $libroCajaDetalle = $em->getRepository('AppBundle:LibroCajaDetalle')->findBy(Array('libroCaja' => $libroCaja, 'activo' => 1))[0];

            $libroCajaDetalle->setImporte($libroCaja->getSaldoInicial());
            $libroCajaDetalle->setUpdatedBy($this->getUser()->getId());
            $libroCajaDetalle->setUpdatedAt(new \DateTime("now"));


            $em->flush();

            return $this->redirectToRoute('librocaja_show', array('id' => $libroCaja->getId()));
        }

        return $this->render('librocaja/edit.html.twig', array(
            'libroCaja' => $libroCaja,
            'edit_form' => $editForm->createView(),
            'libroCajaDetalles' => $libroCajaDetalles,
            'ingresos' => $ingresos,
            'egresos' => $egresos,
        ));
    }

    /**
     * Deletes a libroCaja entity.
     *
     */
    public function deleteAction(Request $request, LibroCaja $libroCaja)
    {
        $form = $this->createDeleteForm($libroCaja);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($libroCaja);
            $em->flush();
        }

        return $this->redirectToRoute('librocaja_index');
    }

    /**
     * Creates a form to delete a libroCaja entity.
     *
     * @param LibroCaja $libroCaja The libroCaja entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LibroCaja $libroCaja)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('librocaja_delete', array('id' => $libroCaja->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
