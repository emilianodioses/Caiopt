<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class LoadController extends Controller 
{

    public function loadingAction(Request $request) {
        
        $session = $request->getSession();

        $user = $this->getUser();

        $redirect = 'homepage'; 

        return $this->redirect($this->generateUrl($redirect));
    } 



    public function loginAction(Request $request)
    {

        //$em = $this->getDoctrine()->getManager();
        //$sucursal = $em->getRepository('AppBundle:Sucursal')->findAll();

        $authenticationUtils = $this->get('security.authentication_utils');

        $session = $request->getSession();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();  

        return $this->render('load/login.html.twig', array(
            'last_username' => $lastUsername,
            //'sucursales' => $sucursal,
            'error'         => $error,
        ));
    }

    public function logoutAction() {

    } 

}
