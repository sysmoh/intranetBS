<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Famille;
use FOS\ElasticaBundle\Configuration\Search;




/**
 * Class CreanceToFamille
 * @package Interne\FinancesBundle\Entity
 * @ORM\Entity
 * @Search(repositoryClass="Interne\FinancesBundle\SearchRepository\CreanceToFamilleRepository")
 */
class CreanceToFamille extends Creance
{
    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="creances")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     */
    private $famille;

    /**
     * Set famille
     *
     * @param Famille $famille
     * @return Creance
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return Famille
     */
    public function getFamille()
    {
        return $this->famille;
    }

    /**
     * Get owner
     */
    public function getOwner()
    {
        return $this->famille;
    }

    /**
     * Conversion de Creance à CranceToFamille
     *
     * @param Creance $creance
     */
    function loadFromCreance(Creance $creance)
    {
        $this->facture = $creance->facture;
        $this->titre = $creance->titre;
        $this->remarque = $creance->remarque;
        $this->montantEmis = $creance->montantEmis;
        $this->montantRecu = $creance->montantRecu;
        $this->dateCreation = $creance->dateCreation;
    }
}