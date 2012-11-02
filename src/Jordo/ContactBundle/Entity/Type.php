<?php

namespace Jordo\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ContactBundle\Entity\Type
 *
 * @ORM\Table(name="contact_type")
 * @ORM\Entity
 */
class Type
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $fname
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="\Jordo\ContactBundle\Entity\Contact", mappedBy="type")
     */
    protected $contact;


    public function __toString()
    {
        return (string) $this->title;
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
     * Set title
     *
     * @param string $title
     * @return Type
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}