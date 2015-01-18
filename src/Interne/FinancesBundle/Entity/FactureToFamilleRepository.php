<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FactureToFamilleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FactureToFamilleRepository extends EntityRepository
{
    public function findByOwner($nom)
    {
        $queryBuilder = $this->createQueryBuilder('facture');
        $queryBuilder->leftJoin('AppBundle\Entity\Famille', 'famille', 'WITH', 'famille.id = creance.famille');

        if ($nom != null) {

            $queryBuilder->andWhere($queryBuilder->expr()->like('famille.nom',$queryBuilder->expr()->literal('%'.$nom.'%')) );

        }

        $results = $queryBuilder->getQuery()->getResult();

        $idAarry = array();

        foreach($results as $result)
        {
            array_push($idAarry,$result->getId());
        }

        return $idAarry;

    }

}