<?php

namespace AppBundle\Repository;

/**
 * ArticuloRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticuloRepository extends \Doctrine\ORM\EntityRepository
{
	public function findByTexto($texto) {
        $query = 'SELECT a  FROM AppBundle:Articulo a ';
        $query.= ' INNER JOIN AppBundle:ArticuloMarca am WITH a.marca = am.id';

        $query .= ' WHERE a.activo = 1';

        if ($texto != ''){
            $query .= ' AND (a.codigo LIKE :texto OR a.descripcion LIKE :texto OR am.descripcion LIKE :texto)';
        }

        $query .= ' ORDER BY a.codigo ASC ';

        $qb = $this->getEntityManager()->createQuery($query);

        if ($texto != '')
            $qb->setParameter('texto','%' . $texto . '%');


        return $qb;
    }

    public function findByAjuste($marca, $categoria) {
        $query = 'SELECT a.id, a.codigo, a.precioVenta, am.descripcion as descMarca, ac.descripcion as descCat, a.precioVenta, a.descripcion FROM AppBundle:Articulo a ';
        $query.= 'INNER JOIN a.marca am ';
        $query.= 'INNER JOIN a.categoria ac ';
        $query.= "WHERE 1=1 ";
        if ($marca <> 0)
            $query.= "AND am.id = :marca ";
        if ($categoria <> 0)
            $query.= "AND ac.id = :categoria ";
        $query.= 'ORDER BY a.marca ASC ';

        $qb = $this->getEntityManager()->createQuery($query);

        if ($marca <> 0)
            $qb->setParameter('marca',$marca);
        if ($categoria <> 0)
            $qb->setParameter('categoria',$categoria);
        return $qb->getArrayResult();
    }

    public function updateByAjuste($marca, $categoria, $porcentaje, $updated_by, $update_at) {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query = $queryBuilder->update('AppBundle:Articulo', 'a')
            ->set('a.precioVenta', 'a.precioVenta*(1+(:ajuste/100))')
            ->set('a.updatedBy', ':id_usuario')
            ->set('a.updatedAt', ':updated_at')
            ->setParameter('ajuste', $porcentaje)
            ->setParameter('id_usuario', $updated_by)
            ->setParameter('updated_at', $update_at);


        if ($marca <> 0){
            $query->andWhere('a.marca = :marca');
            $query->setParameter('marca', $marca);
        }

        if ($categoria <> 0){
            $query->andWhere('a.categoria = :categoria');
            $query->setParameter('categoria',$categoria);
        }

        $query->getQuery()->execute();
        
    }
}
