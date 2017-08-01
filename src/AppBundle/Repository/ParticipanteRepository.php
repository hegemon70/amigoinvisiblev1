<?php

namespace AppBundle\Repository;
use \Doctrine\ORM\EntityRepository;
/**
 * ParticipanteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParticipanteRepository extends EntityRepository
{
	public function findBySinSorteo()
	{
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.idSorteo IS NULL');

        $idParticipantes = $qb->getQuery()->getArrayResult();
       
        return $idParticipantes;
	}

}