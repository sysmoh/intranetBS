<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;
use FOS\ElasticaBundle\Configuration\Search;

/**
 *
 *
 *
 * Class Facture
 *
 * @ORM\Table(name="app_factures")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactureRepository")
 * @Search(repositoryClass="AppBundle\Search\Facture\FactureRepository")
 *
 */
class Facture
{
    const PAYED = 'payed';
    const OPEN = 'open';
    const CANCELLED = 'cancelled';

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
     * @ORM\OneToMany(targetEntity="Rappel",mappedBy="facture", cascade={"persist", "remove"})
     */
    private $rappels;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Creance",mappedBy="facture", cascade={"persist"})
     */
    private $creances;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", columnDefinition="ENUM('open','payed','cancelled')")
     */
    private $statut = Facture::OPEN;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    private $dateCreation;

    /**
     * @var Payement
     *
     * @ORM\OneToOne(targetEntity="Payement", mappedBy="facture", cascade={"persist"})
     */
    private $payement;


    /**
     * @var Debiteur
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Debiteur", inversedBy="factures")
     * @ORM\JoinColumn(name="debiteur_id", referencedColumnName="id")
     */
    private $debiteur;


    public function __construct()
    {
        $this->rappels = new ArrayCollection();
        $this->creances = new ArrayCollection();
    }


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
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    public function isPayed()
    {
        return (Facture::PAYED == $this->statut? true:false);
    }

    public function isOpen()
    {
        return (Facture::OPEN == $this->statut? true:false);
    }

    public function isCancelled()
    {
        return (Facture::CANCELLED == $this->statut? true:false);
    }
    /**
     * Set statut
     *
     * @param string $statut
     * @return Facture
     */
    public function setStatut($statut)
    {

        if ($statut != Facture::OPEN && $statut != Facture::PAYED  && $statut != Facture::CANCELLED)
            throw new Exception("Le statut doit être "
                . Facture::OPEN . "|"
                . Facture::PAYED . "|"
                . Facture::CANCELLED  . ", obtenu : '" . $statut . "'");

        $this->statut = $statut;

        return $this;
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
     * Get rappels
     *
     * @return ArrayCollection
     */
    public function getRappels()
    {
        return $this->rappels;
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

        foreach ($rappels as $rappel) {
            $rappel->setFacture($this);
        }

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
     * Get datePayement
     *
     * @return \DateTime
     */
    public function getDatePayement()
    {
        if($this->payement == null)
            return null;
        else
            return $this->payement->getDate();
    }

    /**
     * Set datePayement
     *
     * @param \DateTime $dateTime
     * @return Facture
     */
    public function setDatePayement(\DateTime $dateTime)
    {

        if ($this->payement != null)
            $this->payement->setDate($dateTime);

        return $this->payement;
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
     * Get creances
     *
     * @return ArrayCollection
     */
    public function getCreances()
    {
        return $this->creances;
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

        foreach ($creances as $creance) {
            $creance->setFacture($this);
        }

        return $this;
    }

    /**
     * Get payement
     *
     * @return \AppBundle\Entity\Payement
     */
    public function getPayement()
    {
        return $this->payement;
    }

    /**
     * Set payement
     *
     * @param \AppBundle\Entity\Payement $payement
     *
     * @return Facture
     */
    public function setPayement(\AppBundle\Entity\Payement $payement = null)
    {
        $this->payement = $payement;

        return $this;
    }


    /**
     * @return bool
     */
    public function hasPayement()
    {
        return ($this->payement != null ? true:false);
    }

    /**
     * Set debiteur
     *
     * @param \AppBundle\Entity\Debiteur $debiteur
     *
     * @return Facture
     */
    public function setDebiteur(\AppBundle\Entity\Debiteur $debiteur = null)
    {
        $this->debiteur = $debiteur;
        if(!$debiteur->getFactures()->contains($this))
        {
            $debiteur->addFacture($this);
        }
        return $this;
    }

    /**
     * Get debiteur
     *
     * @return \AppBundle\Entity\Debiteur
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * Si un payement à été recu, il n'est plus possible de supprimer la facture
     *
     * @return bool
     */
    public function isRemovable()
    {
        return !$this->hasPayement();
    }

}
