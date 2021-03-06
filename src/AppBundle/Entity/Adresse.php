<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adresse
 *
 * @Gedmo\Loggable
 * @ORM\Table(name="app_adresses")
 * @ORM\Entity
 */
class Adresse
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
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="expediable", type="boolean")
     */
    private $expediable;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="rue", type="string", length=255, nullable=true)
     */
    private $rue;

    /**
     * @var integer
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="npa", type="integer", nullable=true)
     */
    private $npa;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="localite", type="string", length=255, nullable=true)
     */
    private $localite;


    /**
     * @var Contact
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Contact", inversedBy="adresse")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $contact;


    public function __construct($rue = '', $npa = null, $localite = '', $expediable = false)
    {
        $this->rue = $rue;
        $this->npa = $npa;
        $this->localite = $localite;
        $this->expediable = $expediable;
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


    public function __tostring()
    {
        return  $this->getRue() . ', ' . $this->getNpa() . ' ' . $this->getLocalite();
    }

    public function toPostalFormat()
    {
        return  $this->getRue() .PHP_EOL. $this->getNpa() . ' ' . $this->getLocalite();
    }

    /**
     * Get rue
     *
     * @return string
     */
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * Set rue
     *
     * @param string $rue
     * @return Adresse
     */
    public function setRue($rue)
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * Get npa
     *
     * @return integer
     */
    public function getNpa()
    {
        return $this->npa;
    }

    /**
     * Set npa
     *
     * @param integer $npa
     * @return Adresse
     */
    public function setNpa($npa)
    {
        $this->npa = $npa;

        return $this;
    }

    /**
     * Get localite
     *
     * @return string
     */
    public function getLocalite()
    {
        return $this->localite;
    }

    /**
     * Set localite
     *
     * @param string $localite
     * @return Adresse
     */
    public function setLocalite($localite)
    {
        $this->localite = $localite;

        return $this;
    }

    /**
     * is expediable
     *
     * @return bool
     */
    public function isExpediable()
    {
        return $this->expediable;
    }

    /**
     * Get expediable
     *
     * @return boolean
     */
    public function getExpediable()
    {
        return $this->expediable;
    }

    /**
     * Set expediable
     *
     * @param boolean $expediable
     * @return Adresse
     */
    public function setExpediable($expediable)
    {
        $this->expediable = $expediable;

        return $this;
    }



    /**
     * Get contact
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param \AppBundle\Entity\Contact $contact
     *
     * @return Contact
     */
    public function setContact(\AppBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }
}
