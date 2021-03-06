<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\ElasticaBundle\Annotation\Search;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * Membre
 *
 * @ORM\Entity
 * @Gedmo\Loggable
 * @ORM\Table(name="app_membres")
 * @Search(repositoryClass="AppBundle\Search\Membre\MembreRepository")
 *
 * @ExclusionPolicy("all")
 */
class Membre extends Personne implements ExpediableInterface, DebiteurInterface, ReceiverInterface
{

    const Facture_to_famille = 'Famille';
    const Facture_to_membre = 'Membre';


    use RemarquableTrait;
    /**
     * @var Famille
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Famille", inversedBy="membres", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     * @Gedmo\Versioned
     *
     * JMS annotations:
     * @Expose
     * @MaxDepth(2)
     */
    private $famille;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Attribution", mappedBy="membre", cascade={"persist", "remove"})
     */
    private $attributions;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ObtentionDistinction", mappedBy="membre", cascade={"persist", "remove"})
     */
    private $distinctions;

    /**
     * @var \Datetime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="naissance", type="date")
     *
     *
     */
    private $naissance;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="numero_bs", type="integer", nullable=true)
     *
     *
     *
     */
    private $numeroBs;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="numero_avs", type="string", length=255, nullable=true)
     */
    private $numeroAvs;

    /**
     * @var \Datetime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="inscription_date", type="date", nullable=true)
     *
     * Doit pouvoir etre null en cas de présence dans la DB sans reception de l'inscription
     */
    private $inscriptionDate;

    /**
     * @var \Datetime
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="desincription_date", type="date", nullable=true)
     *
     *  Doit pouvoir etre null lorsque le membre n'est pas encore désinscrit
     *
     */
    private $desinscriptionDate;


    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;



    /**
     * Cette propriété détermine si les cérances détenues par ce membre sont facturées
     * à la famille ou au membre lui même.
     *
     * @var string envoiFacture
     *
     * @ORM\Column(name="envoi_facture", type="string", columnDefinition="ENUM('Famille', 'Membre')")
     *
     */
    private $envoiFacture = self::Facture_to_famille;

    /**
     * @var DebiteurMembre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\DebiteurMembre",
     *                inversedBy="membre", cascade={"persist","remove"})
     */
    private $debiteur;

    /**
     * @var ReceiverMembre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ReceiverMembre",
     *                inversedBy="membre", cascade={"persist","remove"})
     */
    private $receiver;

    /**
     * @var SenderMembre
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\SenderMembre",
     *                inversedBy="membre", cascade={"persist","remove"})
     */
    private $sender;


    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->inscriptionDate = null;
        $this->desinscriptionDate = null;
        $this->naissance = new \Datetime();
        $this->validity = true;

        //un membre a forcement un debiteur
        $this->debiteur = new DebiteurMembre();
        $this->debiteur->setMembre($this);

        //un membre a forcement un receiver
        $this->receiver = new ReceiverMembre();
        $this->receiver->setMembre($this);

        //un membre a forcement un sender
        $this->sender = new SenderMembre();
        $this->sender->setMembre($this);

        $this->attributions = new ArrayCollection();

    }


    /*
     * ===== MatBundle
     *
    /*
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="membre", cascade={"persist", "remove"})
     *
    private $bookings;
    //*/



    /**
     * Représentation string du membre
     * On ajoute le numéro BS pour le log de données sur lequel on effectuera des recherches
     *
     * @return string
     */
    public function __toString() {
        return $this->getPrenom() . ' ' . $this->getNom();
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        if ($this->getFamille() != null)
            return ucwords($this->getFamille()->getNom());
        else
            return "[NO_FAMILY]";
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
     * Set famille
     *
     * @param Famille $famille
     * @return Membre
     */
    public function setFamille($famille)
    {
        $this->famille = $famille;
        if(!$famille->getMembres()->contains($this))
        {
            $famille->addMembre($this);
        }
        return $this;
    }

    /**
     * Get distinctions
     *
     * @return Collection
     */
    public function getDistinctions()
    {
        return $this->distinctions;
    }

    /**
     * Set distinctions
     *
     * @param array $distinctions
     * @return Membre
     */
    public function setDistinctions($distinctions)
    {
        $this->distinctions = $distinctions;

        return $this;
    }

    /**
     * Get numeroBs
     *
     * @return integer
     */
    public function getNumeroBs()
    {
        return $this->numeroBs;
    }

    /**
     * Set numeroBs
     *
     * @param integer $numeroBs
     * @return Membre
     */
    public function setNumeroBs($numeroBs)
    {
        $this->numeroBs = $numeroBs;

        return $this;
    }

    /**
     * Get numeroAvs
     *
     * @return string
     */
    public function getNumeroAvs()
    {
        return $this->numeroAvs;
    }

    /**
     * Set numeroAvs
     *
     * @param string $numeroAvs
     * @return Membre
     */
    public function setNumeroAvs($numeroAvs)
    {
        $this->numeroAvs = $numeroAvs;

        return $this;
    }

    /**
     * Get naissance
     *
     * @return \DateTime
     */
    public function getNaissance()
    {
        return $this->naissance;
    }

    /**
     * Set naissance
     *
     * @param \DateTime $naissance
     * @return Membre
     */
    public function setNaissance($naissance)
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * Get inscription
     *
     * @return \DateTime|null
     */
    public function getInscriptionDate()
    {
        return $this->inscriptionDate;
    }

    /**
     * Set inscription
     *
     * @param \DateTime $inscriptionDate
     * @return Membre
     */
    public function setInscriptionDate(\DateTime $inscriptionDate)
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    /**
     * Add attributions
     *
     * @param \AppBundle\Entity\Attribution $attribution
     * @return Membre
     */
    public function addAttribution(\AppBundle\Entity\Attribution $attribution)
    {
        $this->attributions->add($attribution);
        if($attribution->getMembre() != $this)
        {
            $attribution->setMembre($this);
        }
        return $this;
    }

    /**
     * Remove attributions
     *
     * @param \AppBundle\Entity\Attribution $attributions
     */
    public function removeAttribution(\AppBundle\Entity\Attribution $attributions)
    {
        $this->attributions->removeElement($attributions);
    }

    /**
     * Get attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributions()
    {
        return $this->attributions;
    }

    /**
     * Retourne la première attribution active si elle existe
     */
    public function getActiveAttribution() {
        $today = new \Datetime("now");

        /** @var Attribution $attr */
        foreach($this->attributions as $attr) {

            if($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                return $attr;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasActiveAttributions()
    {
        if ($this->getActiveAttributions()->isEmpty())
            return false;
        else
            return true;
    }

    /**
     * Get active attributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveAttributions()
    {
        $attrs = new ArrayCollection();
        $today = new \Datetime("now");

        /** @var Attribution $attr */
        foreach ($this->attributions as $attr) {
            if ($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                $attrs[] = $attr;
        }

        return $attrs;
    }

    /**
     * Get active attributions
     *
     * Retourne les attributions active spécifique à
     * un groupe.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActiveAttributionsForGroupe(Groupe $groupe)
    {
        $attrs = new ArrayCollection();
        $today = new \Datetime("now");

        /** @var Attribution $attr */
        foreach ($this->attributions as $attr) {
            if ($attr->getDateFin() >= $today || $attr->getDateFin() == null)
                if($attr->getGroupe() == $groupe)
                    $attrs->add($attr);
        }

        return $attrs;
    }

    /**
     * Retourne un tableau de groupe ou le membre est actuellemnt.
     * @return Groupe[]
     */
    public function getActiveGroupes()
    {
        /** @var Attribution[] $attributions */
        $attributions = $this->getActiveAttributions();

        /** @var Groupe[] $groups */
        $groups = array();

        /** @var Attribution $attribution */
        foreach($attributions as $attribution) {
            $groups[] = $attribution->getGroupe();
        }
        return $groups;
    }



    public function getUsername()
    {
        // TODO: récupérer le username depuis le User
        /*
         * de uffer: a mon avis cette fonction n'a pas de sens. il faudrais plustot rendre le one to one avec le user bidirectionel et le récupéré comme ca
         */
        return strtolower($this->getPrenom()) . '.' . strtolower($this->getNom());
    }

    /**
     * Add distinction
     *
     * @param \AppBundle\Entity\ObtentionDistinction $distinction
     * @return Membre
     */
    public function addDistinction(\AppBundle\Entity\ObtentionDistinction $distinction)
    {
        $this->distinctions[] = $distinction;
        $distinction->setMembre($this);
        return $this;
    }

    /**
     * Remove distinction
     *
     * @param \AppBundle\Entity\ObtentionDistinction $distinction
     */
    public function removeDistinction(\AppBundle\Entity\ObtentionDistinction $distinction)
    {
        $this->distinctions->removeElement($distinction);
    }

    /**
     * Get validity
     *
     * @return integer
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set validity
     *
     * @param integer $validity
     * @return Membre
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get envoiFacture
     *
     * @return string
     */
    public function getEnvoiFacture()
    {
        return $this->envoiFacture;
    }

    /**
     * @param $envoiFacture
     * @return $this
     * @throws \Exception
     */
    public function setEnvoiFacture($envoiFacture)
    {
        if(($envoiFacture == self::Facture_to_famille) || ($envoiFacture == self::Facture_to_membre))
            $this->envoiFacture = $envoiFacture;
        else
            throw new \Exception("envoie facture should be membre or famille");
        return $this;
    }

    /**
     * Retourne l'adresse d'expédition du membre, c'est-à-dire son adresse principale
     * @return array|null
     */
    public function getAdresseExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getAdresse();
    }


    public function getListeEmailsExpedition()
    {
        $expediable = new Expediable($this);
        return $expediable->getListeEmails();
    }


    /**
     * Get historique
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistorique()
    {
        return $this->historique;
    }

    /**
     * @param \AppBundle\Entity\Creance $creance
     * @return Membre
     */
    public function addCreance(Creance $creance)
    {
        $this->getDebiteur()->addCreance($creance);
        return $this;
    }

    /**
     * Get debiteur
     *
     * @return \AppBundle\Entity\DebiteurMembre
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * Set debiteur
     *
     * @param \AppBundle\Entity\DebiteurMembre $debiteur
     *
     * @return Membre
     */
    public function setDebiteur($debiteur = null)
    {
        $this->debiteur = $debiteur;
        $debiteur->setMembre($this);
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Facture $facture
     * @return Membre
     */
    public function addFacture(Facture $facture)
    {
        $this->getDebiteur()->addFacture($facture);
        return $this;
    }

    /**
     * Get receiver
     *
     * @return ReceiverMembre
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set receiver
     *
     * @param ReceiverMembre $receiver
     *
     * @return Membre
     */
    public function setReceiver(ReceiverMembre $receiver = null)
    {
        $this->receiver = $receiver;
        if(is_null($receiver->getMembre()))
            $receiver->setMembre($this);
        return $this;
    }

    /**
     * Get sender
     *
     * @return SenderMembre
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set sender
     *
     * @param SenderMembre $sender
     *
     * @return Membre
     */
    public function setSender(SenderMembre $sender = null)
    {
        $this->sender = $sender;
        if(is_null($sender->getMembre()))
            $sender->setMembre($this);
        return $this;
    }

    public function addMail(Mail $mail)
    {
        $this->getReceiver()->addMail($mail);
        return $this;
    }

    /**
     * @return \Datetime
     */
    public function getDesinscriptionDate()
    {
        return $this->desinscriptionDate;
    }

    /**
     * @param \Datetime $desinscriptionDate
     */
    public function setDesinscriptionDate(\DateTime $desinscriptionDate)
    {
        $this->desinscriptionDate = $desinscriptionDate;
    }





}
