<?php

namespace AppBundle\Repository;

/**
 * SorteoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SorteoRepository extends \Doctrine\ORM\EntityRepository
{

  public function findByCodigoSorteo($codigo)
  {
       return $this->getEntityManager()
       ->createQuery('SELECT s FROM AppBundle:Sorteo s WHERE ( s.codigoSorteo = ?1)ORDER BY s.id DESC')
       ->setParameter(1,$codigo)
       ->setMaxResults(1)
       ->getSingleResult();
  }

  public function findByCodigoSorteoId($codigo)
  {
       return $this->getEntityManager()
       ->createQuery('SELECT s.id FROM AppBundle:Sorteo s WHERE ( s.codigoSorteo = ?1)ORDER BY s.id DESC')
       ->setParameter(1,$codigo)
       ->setMaxResults(1)
       ->getSingleResult();
  }
}
