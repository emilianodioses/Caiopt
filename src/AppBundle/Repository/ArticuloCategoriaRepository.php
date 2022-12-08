<?php

namespace AppBundle\Repository;

/**
 * ArticuloCategoriaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticuloCategoriaRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByTexto($texto) {
        $query = 'SELECT c  FROM AppBundle:ArticuloCategoria c ';

        if ($texto != '')
            $query .= ' WHERE c.descripcion LIKE :texto';

        $query .= ' ORDER BY c.descripcion ASC ';

        $em = $this->getEntityManager()->createQuery($query);

        if ($texto != '')
            $em->setParameter('texto','%' . $texto . '%');
        return $em;
    }
}