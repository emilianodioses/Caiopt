<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

/* * ****************************************************************************
 * PanelEventListener
 *
 *   Provee un ACL a nivel de controlador/accion que controla al momento de
 * los request de Symfony, las otras validaciones de autentificacion y
 * autorizacion las realiza Symfony
 *
 * @author guido
 * *************************************************************************** */

class AppEventListener {

  protected $em;
  protected $container;

  function __construct(ContainerInterface $container, EntityManager $em) {
    $this->container = $container;
    $this->em = $em;
  }

  /*   * ************************************************************************
   *   Symfony se encarga de la parte de autorizazión básica, luego de la
   * autentificación solo tiene 2 roles:'usuario logeado' y 'usuario anonimo'
   * En esta función controlamos el ACL a nivel de controlador/accion:
   *   * Si es un usuario anonimo, Symfony se encargará de permitir o
   *  reenviarlo al login
   *   * Si el usuario existe, lo obtenemos y chequeamos en la DB si tiene
   *  permiso o no.
   *
   *   Warinig: Solo se validan las master request.
   * *********************************************************************** */

  public function onKernelRequest(GetResponseEvent $event) {
    /*     * ********************************************************************
     *  Asumimos que si no se puede obtener el usuario atraves del
     * security.context es porque el usuario no esta logeado
     * ******************************************************************* */
    $user_id = -1;
    //Para symfony2
    //$context = $this->container->get('security.context');
    //Para symfony3
    $context = $this->container->get('security.token_storage');
    if ($context)
      if ($context->getToken())
        if ((($context->getToken()->getUser()) != null) && ('anon.' != ($context->getToken()->getUser())))
          $user_id = $context->getToken()->getUser()->getId();


    /*     * ********************************************************************
     *  Unicamente se contralan las master request, esto evita un
     * overhead en este punto que es critico pero se pierde granularidad
     * a nivel de control de acceso
     * ******************************************************************* */
    if (HttpKernel::MASTER_REQUEST == $event->getRequestType()) {


      /*       * ****************************************************************
       *  En los master request el formato de _controller es asi:
       *
       * Hospital\FrontendBundle\Controller\DefaultController::indexAction
       *
       *  Estamos en una etapa previa al ruteo de symfony asi que no
       * contamos con un formato más elegante.
       * *************************************************************** */
      $url = $event->getRequest()->attributes->get('_controller');
      $request = explode('::', $url);

      /*       * ****************************************************************
       * En caso de que no tenga ese formato lo dejamos pasar por ahora.
       * Aparentemente el único caso es:
       * 'web_profiler.controller.profiler:toolbarAction'
       * *************************************************************** */
      if (sizeof($request) != 2) {
        //var_dump($request);
        return;
      }

      $controllerFull = $request[0];
      $controllerExplode = explode('Controller\\', $controllerFull);
      $controllerName = $controllerExplode[1];
      $action = $request[1];

      /* Ignoramos las acciones de login y logout */
      if (($action != 'loginAction') && 
          ($action != 'logoutAction') && 
          ($action != 'urlRedirectAction') && 
          ($user_id != -1) &&
          ($controllerName != 'DashboardController')
        ) {

        $rep = $this->em->getRepository('AppBundle:Usuario');
        $access = $rep->checkPermissions($user_id, $controllerName, $action);

        //$access = array(1, 2);
        //$access = array();
        //if (sizeof($access) == 0) {
        //var_dump($access);
        //die;
        if (!$access) {
          $controllerBusqueda = str_replace("Controller", "", $controllerName);
          $actionBusqueda = str_replace("Action", "", $action);
          $control = $this->em->getRepository('AppBundle:Funcion')->findOneBy(array('modulo' => $controllerBusqueda, 'accion' => $actionBusqueda));
          
          $mens = "<b>Acceso denegado:</b><br/>";
          $mens .= "   Solicite acceso a su administrador: ";

          $env = $this->container->get("kernel")->getEnvironment();

          //if ($env != 'prod') {
            //$mens .= $env . " - " . $controllerName . ":" . $action;
            $mens .= $control->getDescripcion();
          //}
          $mens .= '<br><a href="javascript:window.history.back();">&laquo; Volver atrás</a>';
          $event->setResponse(new Response($mens, 403));
          return;
        }
      }
    }

    return;
  }

}