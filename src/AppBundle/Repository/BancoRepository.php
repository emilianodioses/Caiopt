<?php

namespace AppBundle\Repository;

/**
 * BancoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BancoRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByTexto($texto) {
        $query = 'SELECT c  FROM AppBundle:Banco c ';
        
        if ($texto != '')
            $query .= ' WHERE c.nombre LIKE :texto';

        $query .= ' ORDER BY c.nombre ASC ';

        $em = $this->getEntityManager()->createQuery($query);

        if ($texto != '')
            $em->setParameter('texto','%' . $texto . '%');
        return $em;
    }
}
