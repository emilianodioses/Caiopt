<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecureAction 
{
    private $m_em;

    public function __construct(EntityManager $em)
    {
        $this->m_em = $em;
    }
    
    public function isAuthorized($modulo,$accion,$rolid)
    {
        $query = $this->m_em->createQuery(
                    "SELECT count(rf) 
                    FROM AppBundle:RolFuncion rf 
                    JOIN rf.funcion f 
                    WHERE f.accion = :accion
                    AND f.modulo = :modulo
                    AND rf.rol = :rol "
                );
        $query->setParameter('rol', $rolid);
        $query->setParameter('modulo', $modulo);
        $query->setParameter('accion', $accion);
        $funcion = $query->getSingleScalarResult();

        if ($funcion == 0) //No está asignada la función del modulo para ese rol
            return 0;
        else
            return 1;
    }
    
    public function authorize($modulo,$accion,$rolid)
    {
        if ($this->isAuthorized($modulo,$accion,$rolid))
            return 1;
        else 
            throw new AccessDeniedException();
    }
}

