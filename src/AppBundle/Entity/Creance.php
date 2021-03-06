<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Configuration\Search;



/**
 * Class Creance
 *
 * @ORM\Table(name="app_creances")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CreanceRepository")
 * @Search(repositoryClass="AppBundle\Search\Creance\CreanceRepository")
 *
 */
class Creance
{

    use RemarquableTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Facture
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Facture", inversedBy="creances", cascade={"persist"})
     * @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     */
    protected $facture;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    protected $titre;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="date")
     */
    protected $dateCreation;


    /**
     * @var float
     *
     * @ORM\Column(name="montantEmis", type="float")
     */
    protected $montantEmis;

    /**
     * @var float
     *
     * @ORM\Column(name="montantRecu", type="float", nullable=true)
     */
    protected $montantRecu;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Debiteur", inversedBy="creances")
     * @ORM\JoinColumn(name="debiteur_id", referencedColumnName="id")
     */
    private $debiteur;





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
     * Set titre
     *
     * @param string $titre
     * @return Creance
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }


    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Creance
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
     *  Get datePayement
     *
     * Retourne la date de payement de la facture qui lui est associée.
     * (null si la facture n'est pas encore facturée.)
     *
     * @return \DateTime|null
     */
    public function getDatePayement()
    {
        if($this->isPayed())
        {
            return $this->facture->getDatePayement();
        }
        else
            return null;
    }

    /**
     * Set montantEmis
     *
     * @param float $montantEmis
     * @return Creance
     */
    public function setMontantEmis($montantEmis)
    {
        $this->montantEmis = $montantEmis;

        return $this;
    }

    /**
     * Get montantEmis
     *
     * @return float
     */
    public function getMontantEmis()
    {
        return $this->montantEmis;
    }

    /**
     * Set montantRecu
     *
     * @param float $montantRecu
     * @return Creance
     */
    public function setMontantRecu($montantRecu)
    {
        $this->montantRecu = $montantRecu;

        return $this;
    }

    /**
     * Get montantRecu
     *
     * @return float
     */
    public function getMontantRecu()
    {
        return $this->montantRecu;
    }

    /**
     * Set facture
     *
     * @param Facture $facture
     * @return Creance
     */
    public function setFacture($facture)
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return Facture
     */
    public function getFacture()
    {
        return $this->facture;
    }

    /**
     * Is payed
     *
     * Cette méthode regarde si la créance à une facture et si
     * cette facture est payée.
     *
     * @return Boolean
     */
    public function isPayed()
    {
        if($this->isFactured())
            return $this->facture->isPayed();
        else
            return false;
    }

    /**
     * Is open
     *
     * Cette méthode regarde si la créance à une facture et si
     * cette facture est ouverte.
     *
     * @return Boolean
     */
    public function isOpen()
    {
        if($this->isFactured())
            return $this->facture->isOpen();
        else
            return false;
    }

    /**
     * Is payed
     *
     * Cette méthode regarde si la créance à une facture et si
     * cette facture est annulée.
     *
     * @return Boolean
     */
    public function isCancelled()
    {
        if($this->isFactured())
            return $this->facture->isCancelled();
        else
            return false;
    }

    /**
     * Is factured
     *
     * @return Boolean
     */
    public function isFactured()
    {
        if($this->facture != null)
            return true;
        else
            return false;
    }



    /**
     * Set debiteur
     *
     * @param \AppBundle\Entity\Debiteur $debiteur
     *
     * @return Creance
     */
    public function setDebiteur(\AppBundle\Entity\Debiteur $debiteur = null)
    {
        $this->debiteur = $debiteur;


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
}
