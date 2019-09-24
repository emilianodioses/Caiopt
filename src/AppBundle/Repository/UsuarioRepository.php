<?php

namespace AppBundle\Repository;

/**
 * UsuarioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByTexto($texto) {
        $query = 'SELECT u  FROM AppBundle:Usuario u ';

        if ($texto != '')
            $query .= ' WHERE u.nombre LIKE :texto OR u.apellido LIKE :texto OR u.usuario LIKE :texto ';

        $query .= ' ORDER BY u.usuario ASC ';

        $em = $this->getEntityManager()->createQuery($query);

        if ($texto != '')
            $em->setParameter('texto','%' . $texto . '%');
        return $em;
    }

    public function checkPermissions($usuario_id, $controller, $action) {
        //avf.2014.01.13
        // extrae los nombres puros del controlador y la accion, sin los sufijos Controller y Action
        $controllerName = str_replace("Controller", "", $controller);
        $actionName = str_replace("Action", "", $action);

        $em = $this->getEntityManager('default');
        
        $usuario_activo = $em->createQuery("select count(u.id) from AppBundle:Usuario u
          where u.id = :usuario_id
          and u.activo = 1 ")
                ->setParameter('usuario_id', $usuario_id)
                ->getSingleScalarResult();
                
        if ($usuario_activo == 0) {
          // CASO: EL USUARIO FIGURA DESACTIVADO
          return false;
        }
        
        $control = $em->createQuery('select count(f.id) from AppBundle:Funcion f
          where f.modulo = :controller
          and f.accion = :action
          ')
                ->setParameter('controller', $controllerName)
                ->setParameter('action', $actionName)
                ->getSingleScalarResult();

        if ($control == 0) {
          // CASO: EL <CONTROLADOR-ACCION> NO SE CONTROLA
          // PERMISO HABILITADO
          return true;
        } 
        else {
            /* #####
            Lo siguiente se deshabilita ya que en el sistema contini solo se
            utilizan roles y no hay permisos específicos por usuario
            ########*/
            /*
          //BUSCO SI EL USUARIO TIENE O NO PERMISOS ESPECÍFICOS  
          $permiso_usuario = $em->createQuery('select uucc.usuctrlAcc from AppBundle:TusuariosControles uucc
            inner join uucc.con as cc
            inner join cc.form as ff
            where cc.conDesc = :ca
            and uucc.usu = :usuId
            ')
                  ->setParameter('ca', $controllerName . ':' . $actionName)
                  ->setParameter('usuId', $usuario_id)
                  ->getOneOrNullResult();
          
          //VERIFICO SI EL USUARIO TIENE PERMISOS ESPECÍFICOS SOBRE EL CONTROL
          if(is_null($permiso_usuario)) {
              //NO TIENE PERMISOS ESPECIFICOS
              //BUSCO LOS PERMISOS DEL ROL
              $permiso_rol = $em->createQuery('select count(rrcc.rolctrlAcc) from AppBundle:TrolesControles rrcc
                inner join rrcc.con as cc
                inner join cc.form as ff
                where cc.conDesc = :ca
                and rrcc.rolctrlAcc = :rolCtrlAcc
                and identity(rrcc.rol) IN
                  (SELECT identity(uurr.rol) FROM AppBundle:TusuariosRoles uurr
                   where identity(uurr.usu) = :usuId
                  )
                ')
                      ->setParameter('ca', $controllerName . ':' . $actionName)
                      ->setParameter('rolCtrlAcc', '1')
                      ->setParameter('usuId', $usuario_id)
                      ->getSingleScalarResult();
              
              return ($permiso_rol > 0);
          }
          else {
              //TIENE PERMISOS ESPECIFICOS
              return ($permiso_usuario['usuctrlAcc']);
          }
          */

          //BUSCO LOS PERMISOS DEL ROL
          $permiso_rol = $em->createQuery('select count(rf.id) from AppBundle:RolFuncion rf
            inner join rf.funcion as f
            where f.modulo = :controller
            and f.accion = :action
            and rf.rol = 1
            and identity(rf.rol) IN
              (SELECT identity(u.rol) FROM AppBundle:Usuario u
               where u.id = :usuario_id
              )
            ')
                  ->setParameter('controller', $controllerName)
                  ->setParameter('action', $actionName)
                  ->setParameter('usuario_id', $usuario_id)
                  ->getSingleScalarResult();
          
          return ($permiso_rol > 0);
        }
    }
}
