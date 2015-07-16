<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Interne\FinancesBundle\Entity\CreanceToFamille;
use Interne\FinancesBundle\Entity\FactureToFamille;
use Symfony\Component\Validator\Constraints as Assert;

//FinancesBundle

/**
 * Famille
 *
 * @ORM\Entity
 * @ORM\Table(name="app_familles")
 */
class Famille implements ExpediableInterface
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Membre", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="membres_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $membres;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Pere", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="pere_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $pere;

    /**
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Mere", mappedBy="famille", cascade={"persist"})
     * @ORM\JoinColumn(name="mere_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $mere;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = "2")
     */
    private $nom;

    /**
     * @var integer
     *
     * @ORM\Column(name="validity", type="integer")
     */
    private $validity;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $contact;



    /**
     * @var ArrayCollection
     *
     * @ORM\OneToOne(targetEntity="Interne\FinancesBundle\Entity\DebiteurFamille",
     *                inversedBy="famille", cascade={"persist","remove"})
     */
    private $debiteur;


    /**
     * Constructor
     *
     * @param string $nom
     */
    public function __construct($nom = '')
    {
        $this->membres = new ArrayCollection();

        /*
         * FinancesBundle
         */
        $this->creances = new ArrayCollection();
        $this->factures = new ArrayCollection();

        $this->validity = true;

        $this->nom = $nom;
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
     * Get self
     * @return famille
     */
    public function getSelf() {

        return $this;
    }

    /**
     * Add membres
     *
     * @param \AppBundle\Entity\Membre $membres
     * @return Famille
     */
    public function addMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres[] = $membres;
        $membres->setFamille($this);
        return $this;
    }

    /**
     * Remove membres
     *
     * @param \AppBundle\Entity\Membre $membres
     */
    public function removeMembre(\AppBundle\Entity\Membre $membres)
    {
        $this->membres->removeElement($membres);
    }

    /**
     * Doit renvoyer quelque chose qui permet d'identifier (humainement) une famille
     * Le nom n'est pas suffisant p.ex puisqu'il peut y avoir plusieurs famille avec le même nom
     * du coup on renvoie la localité derrière
     *
     * @return string
     */
    public function __toString()
    {

        $string = "Les " . $this->getNom();

        if ($this->getContact() != null)
        {
            if ($this->getContact()->getAdresse() != NULL) {
                $string .= " de " . $this->getContact()->getAdresse()->getLocalite();
            }
        }


        return $string;

    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return ucwords($this->nom);
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Famille
     */
    public function setNom($nom)
    {
        $this->nom = ucwords($nom);

        return $this;
    }

    /**
     * Get contact
     *
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param Contact $contact
     * @return Famille
     */
    public function setContact(Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
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
     * @return Famille
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Is classe
     *
     * @param string $className
     * @return boolean
     */
    public function isClass($className)
    {
        if ($className == 'Famille')
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return 'Famille';
    }



    public function getAdresseExpedition()
    {
        $adresse = $this->getContact()->getAdresse();
        if (!is_null($adresse)) {
            if ($adresse->isExpediable()) {
                return array('adresse' => $adresse,
                    'owner' => array(
                        'prenom' => null,
                        'nom' => $this->getNom(),
                        'class' => 'Famille',
                    ));
            }
        }

        $mere = $this->getMere();
        if (!is_null($mere)) {
            $adresse = $mere->getContact()->getAdresse();

            if (!is_null($adresse)) {
                if ($adresse->isExpediable()) {
                    return array('adresse' => $adresse,
                        'owner' => array(
                            'prenom' => $mere->getPrenom(),
                            'nom' => $this->getNom(),
                            'class' => 'Famille',
                        ));
                }
            }
        }

        $pere = $this->getPere();
        if (!is_null($pere)) {
            $adresse = $pere->getContact()->getAdresse();

            if (!is_null($adresse)) {
                if ($adresse->isExpediable()) {
                    return array('adresse' => $adresse,
                        'owner' => array(
                            'prenom' => $pere->getPrenom(),
                            'nom' => $this->getNom(),
                            'class' => 'Famille',
                        ));
                }
            }
        }

        return null;
    }

    /**
     * Get mere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getMere()
    {
        return $this->mere;
    }

    /**
     * Set mere
     *
     * @param  Mere $mere
     * @return Famille
     */
    public function setMere($mere = null)
    {
        $this->mere = $mere;
        if ($mere != null) {
            $mere->setFamille($this);
        }
        return $this;
    }

    /**
     * Get pere
     *
     * @return \AppBundle\Entity\Geniteur
     */
    public function getPere()
    {
        return $this->pere;
    }

    /**
     * Set pere
     *
     * @param Pere $pere
     * @return Famille
     */
    public function setPere($pere = null)
    {
        $this->pere = $pere;
        if ($pere != null) {
            $pere->setFamille($this);
        }
        return $this;
    }

    public function getListeEmailsExpedition()
    {
        $liste = array();


        $emails = $this->getContact()->getEmails();
        if (!is_null($emails))
        {
            foreach ($emails as $email) {
                if ($email->isExpediable()) {
                    $liste['Famille'] = $email->getEmail();
                }

            }
        }


        $mere = $this->getMere();
        if(!is_null($mere))
        {
            $emails = $mere->getContact()->getEmails();
            if (!is_null($emails))
            {
                foreach ($emails as $email) {
                    if ($email->isExpediable()) {
                        $liste['Mère'] = $email->getEmail();
                    }

                }
            }
        }


        $pere = $this->getPere();
        if(!is_null($pere))
        {
            $emails = $pere->getContact()->getEmails();
            if (!is_null($emails))
            {
                foreach ($emails as $email) {
                    if ($email->isExpediable()) {
                        $liste['Père'] = $email->getEmail();
                    }

                }
            }
        }


        return $liste;
    }

    public function getListePrenomEnfants()
    {
        $enfants = $this->getMembres();

        $listOfPrenom = array();
        foreach ($enfants as $enfant)
        {
            /** @var Membre $enfant */
            $listOfPrenom[] = $enfant->getPrenom();
        }
        return $listOfPrenom;
    }

    /**
     * Get membres
     *
     * @return array
     */
    public function getMembres()
    {
        return $this->membres;
    }




    /**
     * Set debiteur
     *
     * @param \Interne\FinancesBundle\Entity\DebiteurFamille $debiteur
     *
     * @return Famille
     */
    public function setDebiteur(\Interne\FinancesBundle\Entity\DebiteurFamille $debiteur = null)
    {
        $this->debiteur = $debiteur;
        $debiteur->setFamille($this);
        return $this;
    }

    /**
     * Get debiteur
     *
     * @return \Interne\FinancesBundle\Entity\DebiteurFamille
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * @param \Interne\FinancesBundle\Entity\Creance $creance
     * @return Famille
     */
    public function addCreance($creance)
    {
        $this->getDebiteur()->addCreance($creance);
        return $this;
    }

    /**
     * @param \Interne\FinancesBundle\Entity\Facture $facture
     * @return Famille
     */
    public function addFacture($facture)
    {
        $this->getDebiteur()->addFacture($facture);
        return $this;
    }
}
