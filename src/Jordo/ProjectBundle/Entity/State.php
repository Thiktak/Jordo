<?php

namespace Jordo\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ProjectBundle\Entity\State
 *
 * @ORM\Table(name="project_state")
 * @ORM\Entity
 */
class State
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="user")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\ProjectBundle\Entity\Project", inversedBy="states")
     */
    protected $project;
    
    /**
     * @var datetime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $dateCreated;


    public function __construct()
    {
        $this->dateCreated = $this->dateCreated ?: new \DateTime();
    }

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
     * @return State
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

    /**
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return State
     */
    public function setUser(\Jordo\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return Jordo\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set project
     *
     * @param Jordo\ProjectBundle\Entity\Project $project
     * @return State
     */
    public function setProject(\Jordo\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return Jordo\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return State
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}