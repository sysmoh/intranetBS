<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 * @ORM\Table(name="app_roles")
 * @ORM\Entity
 */
class Role implements RoleInterface
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, unique=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Role", mappedBy="parent", cascade={"persist"}, fetch="EAGER")
     */
    private $enfants;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="enfants", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_role_id", referencedColumnName="id", nullable=true)
     */
    private $parent;



    public function __construct()
    {
        $this->enfants = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Role $parent
     * @return Role
     */
    public function setParent(\AppBundle\Entity\Role $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Role 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add enfants
     *
     * @param \AppBundle\Entity\Role $enfants
     * @return Role
     */
    public function addEnfant(\AppBundle\Entity\Role $enfants)
    {
        $this->enfants[] = $enfants;
        $enfants->setParent($this);
        return $this;
    }

    /**
     * Remove enfants
     *
     * @param \AppBundle\Entity\Role $enfants
     */
    public function removeEnfant(\AppBundle\Entity\Role $enfants)
    {
        $this->enfants->removeElement($enfants);
    }

    /**
     * Get enfants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnfants()
    {
        return $this->enfants;
    }

    public function getEnfantsRecursive($main = false) {

        $enfants = $this->getEnfants()->toArray();

        foreach($enfants as $r)
            $enfants = array_merge($enfants, $r->getEnfantsRecursive());

        if($main) $enfants[] = $this;

        return $enfants;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}