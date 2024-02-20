<?php

namespace AppBundle\Repository;

/**
 * PresupuestoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PresupuestoRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByTexto($texto) {
        $query = 'SELECT a  FROM AppBundle:Presupuesto a
                 INNER JOIN a.cliente c
                 WHERE a.activo = 1';

        if ($texto != '')
            $query .= ' AND (a.id LIKE :texto OR c.nombre LIKE :texto OR c.documentoNumero LIKE :texto) ';

        $query .= ' ORDER BY a.idCliente ASC ';

        $qb = $this->getEntityManager()->createQuery($query);

        if ($texto != '')
            $qb->setParameter('texto','%' . $texto . '%');


        return $qb;
    }

    public function findByIdCliente($idCliente){
        return $this->createQueryBuilder('p')
            ->andWhere('p.idCliente = :idCliente')
            ->setParameter('idCliente', $idCliente)
            ->getQuery()
            ->getResult();
    }
}
