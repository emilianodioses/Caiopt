<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoadController extends Controller 
{

    public function loadingAction(Request $request) {
        $session = $request->getSession();

        $user = $this->getUser();

       $redirect = 'bs_admin_usuario_listado'; 

       return $this->redirect($this->generateUrl($redirect));
    } 



    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $session = $request->getSession();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('load/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function logoutAction() {

    } 

}
