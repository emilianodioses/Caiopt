<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RolFuncion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Rolfuncion controller.
 *
 */
class RolFuncionController extends Controller
{
    /**
     * Lists all rolFuncion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $rolFuncions = $em->getRepository('AppBundle:RolFuncion')->findAll();

        return $this->render('rolfuncion/index.html.twig', array(
            'rolFuncions' => $rolFuncions,
        ));
    }

    /**
     * Finds and displays a rolFuncion entity.
     *
     */
    public function showAction(RolFuncion $rolFuncion)
    {

        return $this->render('rolfuncion/show.html.twig', array(
            'rolFuncion' => $rolFuncion,
        ));
    }
}
