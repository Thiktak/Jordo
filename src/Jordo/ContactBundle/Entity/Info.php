<?php

namespace Jordo\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ContactBundle\Entity\Info
 *
 * @ORM\Table(name="contact_info")
 * @ORM\Entity
 */
class Info
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var $calls
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ContactBundle\Entity\Call", mappedBy="info")
     */
    protected $calls;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=30)
     */
    protected $type;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\ContactBundle\Entity\Contact", inversedBy="info")
     */
    protected $contact;


    public function __toString()
    {
        return (string) sprintf('%s: %s', $this->type, $this->value);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calls = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return Info
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Info
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add calls
     *
     * @param Jordo\ContactBundle\Entity\Call $calls
     * @return Info
     */
    public function addCall(\Jordo\ContactBundle\Entity\Call $calls)
    {
        $this->calls[] = $calls;
    
        return $this;
    }

    /**
     * Remove calls
     *
     * @param Jordo\ContactBundle\Entity\Call $calls
     */
    public function removeCall(\Jordo\ContactBundle\Entity\Call $calls)
    {
        $this->calls->removeElement($calls);
    }

    /**
     * Get calls
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * Set contact
     *
     * @param Jordo\ContactBundle\Entity\Contact $contact
     * @return Info
     */
    public function setContact(\Jordo\ContactBundle\Entity\Contact $contact = null)
    {
        $this->contact = $contact;
    
        return $this;
    }

    /**
     * Get contact
     *
     * @return Jordo\ContactBundle\Entity\Contact 
     */
    public function getContact()
    {
        return $this->contact;
    }
}