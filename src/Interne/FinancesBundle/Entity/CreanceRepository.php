<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CreanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CreanceRepository extends EntityRepository
{
    /*
     * cette fonction est utilisée par le formulaire de recherche de facture.
     * on crée une requete custom.
     */
    /**
     *
     *
     * @param Creance $creance
     * @param null $searchParameters
     * @param bool $recursive
     * @return array
     */
    public function findBySearch(Creance $creance,$searchParameters = null,$recursive = false)
    {
        //on crée un nouvelle requete qui sera custom
        $queryBuilder = $this->createQueryBuilder('creance');

        $queryBuilder->leftJoin('Interne\FinancesBundle\Entity\Facture', 'facture', 'WITH', 'creance.facture = facture.id');
        //$queryBuilder->leftJoin('AppBundle\Entity\Membre', 'membre', 'WITH', 'membre.id = creance.membre');
        //$queryBuilder->leftJoin('AppBundle\Entity\Famille', 'familleDuMembre', 'WITH', 'membre.id = membre.famille');
        //$queryBuilder->leftJoin('AppBundle\Entity\Famille', 'famille', 'WITH', 'famille.id = creance.famille');


        /*
         * Elements de recherche contenu dans le formulaire de creance standard
         */
        $parameter = $creance->getTitre();
        if($parameter != null)
        {
            /*
             * Recherche tout les titre où le bout de texte $parametre est présent
             */
            $queryBuilder->andWhere($queryBuilder->expr()->like('creance.titre',$queryBuilder->expr()->literal('%'.$parameter.'%')) );
        }

        $parameter = $creance->getRemarque();
        if($parameter != null)
        {
            $queryBuilder->andWhere($queryBuilder->expr()->like('creance.remarque',$queryBuilder->expr()->literal('%'.$parameter.'%')) );

        }

        $parameter = $creance->getMontantEmis();
        if($parameter != null)
        {
            $queryBuilder->andWhere('creance.montantEmis = :montantEmis')->setParameter('montantEmis', $parameter);
        }

        $parameter = $creance->getMontantRecu();
        if($parameter != null)
        {
            $queryBuilder->andWhere('creance.montantRecu = :montantRecu')->setParameter('montantRecu', $parameter);
        }

        $parameter = $creance->getDateCreation();
        if($parameter != null)
        {
            $queryBuilder->andWhere('creance.dateCreation = :dateCreation')
                ->setParameter('dateCreation', $parameter);
        }



        /*
         *
         * Elements de recherche spécifique qui permet d'affiner la recherche.
         *
         */

        if($searchParameters['creance'] != null)
        {

            /*
            $parameter = $searchParameters['creance']['isLinkedToFacture'];
            if ($parameter != null) {
                if($parameter == 'yes') //donc la créance est liée
                {
                    $queryBuilder->andWhere($queryBuilder->expr()->isNotNull('creance.facture'));


                    if(($creance->getFacture() != null)&&($recursive == false))
                    {
                        //On récupère la créance contenu dans la facture
                        $facture = $creance->getFacture();

                        //On recherche les créances qui corresponde la créance contenu dans la facture
                        $factures = $this->getEntityManager()->getRepository('InterneFinancesBundle:Facture')->findBySearch($facture,$searchParameters);

                        //On crée la liste des ids de tout les facture trouvée
                        $arrayIds = array();
                        foreach($factures as $facture)
                        {
                            array_push($arrayIds,$facture->getId());
                        }
                        //On cherche tout les cérances liées aux factures trouvée.
                        $queryBuilder->andWhere('facture.id IN (:ids)')->setParameter('ids', array_values($arrayIds));
                    }



                }
                elseif($parameter == 'no')  //donc la cérance n'a pas encore de facture
                {
                    $queryBuilder->andWhere($queryBuilder->expr()->isNull('creance.facture'));

                }
            }

            */
            /*
             * Intervale pour les montants
             */

            $parameter = $searchParameters['creance']['montantEmisMinimum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.montantEmis >= :montantEmisMinimum')
                    ->setParameter('montantEmisMinimum', $parameter);
            }

            $parameter = $searchParameters['creance']['montantEmisMaximum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.montantEmis <= :montantEmisMaximum')
                    ->setParameter('montantEmisMaximum', $parameter);
            }

            $parameter = $searchParameters['creance']['montantRecuMinimum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.montantRecu >= :montantRecuMinimum')
                    ->setParameter('montantRecuMinimum', $parameter);
            }

            $parameter = $searchParameters['creance']['montantRecuMaximum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.montantRecu <= :montantRecuMaximum')
                    ->setParameter('montantRecuMaximum', $parameter);
            }

            /*
             * Intervale date de création
             */

            $parameter = $searchParameters['creance']['dateCreationMaximum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.dateCreation <= :dateCreationMaximum')
                    ->setParameter('dateCreationMaximum', $parameter);
            }
            $parameter = $searchParameters['creance']['dateCreationMinimum'];
            if ($parameter != null) {
                $queryBuilder->andWhere('creance.dateCreation >= :dateCreationMinimum')
                    ->setParameter('dateCreationMinimum', $parameter);
            }




            /*
             * relation avec les membres et famille
             */


            if(($searchParameters['creance']['membreNom'] != null) || ($searchParameters['creance']['membrePrenom'] != null))
            {
                $nom = $searchParameters['creance']['membreNom'];
                $prenom = $searchParameters['creance']['membrePrenom'];

                $idArray = $this->getEntityManager()->getRepository('InterneFinancesBundle:CreanceToMembre')->findByOwner($nom,$prenom);

                if($idArray != null)
                {
                    $queryBuilder->andWhere('creance.id IN (:ids)')->setParameter('ids', array_values($idArray));
                }

            }
            elseif($searchParameters['creance']['familleNom'] != null)
            {
                $nom = $searchParameters['creance']['familleNom'];

                $idArray = $this->getEntityManager()->getRepository('InterneFinancesBundle:CreanceToFamille')->findByOwner($nom);

                if($idArray != null)
                {
                    $queryBuilder->andWhere('creance.id IN (:ids)')->setParameter('ids', array_values($idArray));
                }
            }

        }


        return $queryBuilder->getQuery()->getResult();
    }


    public function getMontantOuvertAtDate(\DateTime $date)
    {
        $queryBuilder = $this->createQueryBuilder('creance')

        ->andWhere('creance.dateCreation <= :dateCreation')
        ->setParameter('dateCreation', $date);

        $queryBuilder->andWhere('creance.datePayement is NULL OR creance.datePayement >= :datePayement')
            ->setParameter('datePayement', $date);

        $queryBuilder->addSelect('SUM(creance.montantEmis) as total');

        $result = $queryBuilder->getQuery()->getScalarResult();

        if($result[0]['total'] == null)
            return 0;
        return floatval($result[0]['total']);
    }

    public function getMontantEmisBetweenDates(\DateTime $start, \DateTime $end)
    {
        $queryBuilder = $this->createQueryBuilder('creance')

            ->andWhere('creance.dateCreation >= :start')
            ->setParameter('start', $start)

            ->andWhere('creance.dateCreation <= :end')
            ->setParameter('end', $end)

            ->addSelect('SUM(creance.montantEmis) as total');


        $result = $queryBuilder->getQuery()->getScalarResult();

        if($result[0]['total'] == null)
            return 0;
        return floatval($result[0]['total']);
    }

    public function getMontantRecuBetweenDates(\DateTime $start, \DateTime $end)
    {
        $queryBuilder = $this->createQueryBuilder('creance')

            ->andWhere('creance.datePayement >= :start')
            ->setParameter('start', $start)

            ->andWhere('creance.datePayement <= :end')
            ->setParameter('end', $end)

            ->addSelect('SUM(creance.montantRecu) as total');


        $result = $queryBuilder->getQuery()->getScalarResult();

        if($result[0]['total'] == null)
            return 0;
        return floatval($result[0]['total']);
    }



}
