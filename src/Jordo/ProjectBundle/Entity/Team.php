<?php

namespace Jordo\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ProjectBundle\Entity\Team
 *
 * @ORM\Table(name="project_team")
 * @ORM\Entity
 */
class Team
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
     * @var integer $jeh
     *
     * @ORM\Column(name="jeh", type="smallint")
     */
    private $jeh;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var $project
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\ProjectBundle\Entity\Project", inversedBy="team")
     */
    private $project;

    /**
     * @var $user
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="teams")
     */
    private $user;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string) $this->user;
    }


    /**
     * Set jeh
     *
     * @param integer $jeh
     * @return Team
     */
    public function setJeh($jeh)
    {
        $this->jeh = $jeh;
    
        return $this;
    }

    /**
     * Get jeh
     *
     * @return integer 
     */
    public function getJeh()
    {
        return $this->jeh;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Team
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set project
     *
     * @param Jordo\ProjectBundle\Entity\Project $project
     * @return Project
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
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return Team
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
}