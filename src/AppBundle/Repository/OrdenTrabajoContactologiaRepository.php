<?php

namespace AppBundle\Repository;

/**
 * OrdenTrabajoContactologiaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrdenTrabajoContactologiaRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAll_sucursal($sucursal_id) {
		$em = $this->getEntityManager('default');
        
		$resultado = $em->createQuery('SELECT ot
                FROM AppBundle:OrdenTrabajoContactologia ot
                INNER JOIN ot.comprobante c
                WHERE ot.activo = 1
                and c.sucursal = :sucursal')
			->setParameter('sucursal', $sucursal_id)
			->getResult();

        return $resultado;
    }

    public function findAll_sucursalFecha($sucursal_id, $fecha) {
        $em = $this->getEntityManager('default');
        
        $resultado = $em->createQuery('SELECT ot
                FROM AppBundle:OrdenTrabajoContactologia ot
                INNER JOIN ot.comprobante c
                WHERE ot.activo = 1
                and c.sucursal = :sucursal
                and c.fecha = :fecha ')
            ->setParameter('sucursal', $sucursal_id)
            ->setParameter('fecha', $fecha)
            ->getResult();

        return $resultado;
    }
}
