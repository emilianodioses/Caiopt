<?php

namespace AppBundle\Repository;

/**
 * ReciboRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReciboRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByTexto($sucursalId, $texto) {
        $query = 'SELECT u  FROM AppBundle:Recibo u 
                  INNER JOIN u.cliente c
                  WHERE u.activo = 1 ';

        if ($sucursalId > 0)
            $query .= ' AND u.sucursal = :sucursalId ';

        if ($texto != '')
            $query .= ' AND (c.nombre LIKE :texto OR c.documentoNumero LIKE :texto) ';

        $query .= ' ORDER BY u.id DESC ';

        $em = $this->getEntityManager()->createQuery($query);

        if ($sucursalId > 0)
            $em->setParameter('sucursalId', $sucursalId);

        if ($texto != '')
            $em->setParameter('texto','%' . $texto . '%');
        
        //return $em;
        return $em->getResult();
    }
}
