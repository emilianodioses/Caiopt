<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
	/**
     * Lists all usuario entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $usuarios = $em->getRepository('AppBundle:Usuario')->findAll();
        
        return $this->render('dashboard/index.html.twig', array(
            'usuarios' => $usuarios,
        ));
    }
}
