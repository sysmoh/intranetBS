<?php

namespace Interne\FinancesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Facture
 *
 * @ORM\Table(name="finances_bundle_factures")
 * @ORM\Entity(repositoryClass="Interne\FinancesBundle\Entity\FactureRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"to_membre" = "FactureToMembre", "to_famille" = "FactureToFamille"})
 */
abstract class Facture implements OwnerInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Rappel",
     *                mappedBy="facture", cascade={"persist", "remove"})
     */
    private $rappels;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Interne\FinancesBundle\Entity\Creance",
     *                mappedBy="facture", cascade={"persist", "remove"})
     */
    private $creances;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", columnDefinition="ENUM('ouverte','payee')")
     */
    private $statut = 'ouverte';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePayement", type="date", nullable=true)
     */
    private $datePayement;


    /*
     * ============= FONCTIONS ============
     */

    public function __construct()
    {
        $this->rappels = new ArrayCollection();
        $this->creances = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    abstract public function getOwner();

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Facture
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
    * Set statut
    *
    * @param string $statut
    * @return Facture
    */
    public function setStatut($statut)
    {

        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Add rappel
     *
     * @param Rappel $rappel
     * @return Facture
     */
    public function addRappel($rappel)
    {
        $this->rappels[] = $rappel;
        $rappel->setFacture($this);

        return $this;
    }

    /**
     * Remove rappel
     *
     * @param Rappel $rappel
     * @return Facture
     */
    public function removeRappel($rappel)
    {
        $this->rappels->remove($rappel);
        $rappel->setFacture(null);

        return $this;
    }

    /**
     * Set rappels
     *
     * @param ArrayCollection $rappels
     * @return Facture
     */
    public function setRappels(ArrayCollection $rappels)
    {
        $this->rappels = $rappels;

        foreach($rappels as $rappel)
        {
            $rappel->setFacture($this);
        }

        return $this;
    }

    /**
     * Get rappels
     *
     * @return ArrayCollection
     */
    public function getRappels()
    {
        return $this->rappels;
    }


    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Facture
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Get montantEmis
     *
     * @return float
     */
    public function getMontantEmis()
    {

        return $this->getMontantEmisCreances() + $this->getMontantEmisRappels();
    }

    /**
     * Get montantEmisCreances
     *
     * @return float
     */
    public function getMontantEmisCreances()
    {
        $montantCreances = 0;
        foreach($this->creances as $creance)
        {
            $montantCreances = $montantCreances + $creance->getMontantEmis();
        }
        return $montantCreances;
    }

    /**
     * Get montantEmisRappels
     *
     * @return float
     */
    public function getMontantEmisRappels()
    {
        $montantRappel = 0;
        foreach($this->rappels as $rappel)
        {
            $montantRappel = $montantRappel + $rappel->getMontantEmis();
        }
        return $montantRappel;
    }

    /**
     * Get montantRecu
     *
     * @return float
     */
    public function getMontantRecu()
    {

        return $this->getMontantRecuCreances() + $this->getMontantRecuRappels();
    }

    /**
     * Get montantRecuCreances
     *
     * @return float
     */
    public function getMontantRecuCreances()
    {
        $montantCreances = 0;
        foreach($this->creances as $creance)
        {
            $montantCreances = $montantCreances + $creance->getMontantRecu();
        }
        return $montantCreances;
    }

    /**
     * Get montantRecuRappels
     *
     * @return float
     */
    public function getMontantRecuRappels()
    {
        $montantRappel = 0;
        foreach($this->rappels as $rappel)
        {
            $montantRappel = $montantRappel + $rappel->getMontantRecu();
        }
        return $montantRappel;
    }


    /**
     * Set datePayement
     *
     * @param \DateTime $datePayement
     * @return Facture
     */
    public function setDatePayement($datePayement)
    {
        $this->datePayement = $datePayement;

        return $this;
    }

    /**
     * Get datePayement
     *
     * @return \DateTime
     */
    public function getDatePayement()
    {
        return $this->datePayement;
    }

    /**
     * Get nombreRappels
     *
     * @return integer
     */
    public function getNombreRappels()
    {
        return $this->rappels->count();
    }

    /**
     * Add creance
     *
     * @param Creance $creance
     * @return Facture
     */
    public function addCreance($creance)
    {
        $this->creances[] = $creance;
        $creance->setFacture($this);

        return $this;
    }

    /**
     * Remove creance
     *
     * @param Creance $creance
     * @return Facture
     */
    public function removeCreance($creance)
    {
        $this->creances->remove($creance);
        $creance->setFacture(null);

        return $this;
    }

    /**
     * Set creances
     *
     * @param ArrayCollection $creances
     * @return Facture
     */
    public function setCreances(ArrayCollection $creances)
    {
        $this->creances = $creances;

        foreach($creances as $creance)
        {
            $creance->setFacture($this);
        }

        return $this;
    }

    /**
     * Get creances
     *
     * @return ArrayCollection
     */
    public function getCreances()
    {
        return $this->creances;
    }

}
