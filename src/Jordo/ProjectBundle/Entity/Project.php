<?php

namespace Jordo\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ProjectBundle\Entity\Project
 *
 * @ORM\Table(name="project_project")
 * @ORM\Entity
 */
class Project
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string $contexte
     *
     * @ORM\Column(name="contexte", type="text", nullable=true)
     */
    protected $contexte;

    /**
     * @var string $demande
     *
     * @ORM\Column(name="demande", type="text", nullable=true)
     */
    protected $demande;

    /**
     * @var float $budget
     *
     * @ORM\Column(name="budget", type="float", nullable=true)
     */
    protected $budget;

    /**
     * @var string $state
     *
     * @ORM\Column(name="state", type="string", length=30, nullable=true)
     */
    protected $state;

    /**
     * @ORM\OneToMany(targetEntity="\Jordo\ProjectBundle\Entity\State", mappedBy="project")
     * @ORM\OrderBY({"dateCreated"="DESC"})
     */
    protected $states;

    /**
     * @var datetime $dateStart
     *
     * @ORM\Column(name="date_start", type="datetime", nullable=true)
     */
    protected $dateStart;

    /**
     * @var datetime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $dateCreated;

    /**
     * @var $gantt
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\GanttBundle\Entity\Gantt", inversedBy="project")
     */
    protected $gantt;

    /**
     * @var $team
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ProjectBundle\Entity\Team", mappedBy="project")
     */
    protected $team;

    /**
     * @var $contact
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\ContactBundle\Entity\Contact", inversedBy="project")
     */
    protected $contact;

    /**
     * @var $documents
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ProjectBundle\Entity\Document", mappedBy="project")
     */
    protected $documents;


    public function __construct()
    {
        $this -> dateCreated = $this -> dateCreated ?: new \DateTime();
    }

    public function __toString()
    {
        return (string) sprintf('%03d %s', $this->id, $this->title);
    }

    public function getProgression()
    {
        return count($this->getDocuments());
    }

    public function getJeh()
    {
        $jeh = 0;
        foreach( $this->team as $team )
            $jeh += $team->getJEH();
        return $jeh;
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
     * @return Project
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
     * Set budget
     *
     * @param float $budget
     * @return Project
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;
    
        return $this;
    }

    /**
     * Get budget
     *
     * @return float 
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Project
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        foreach( $this->states as $state )
            return $state;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Project
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    
        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Project
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

    /**
     * Add team
     *
     * @param Jordo\ProjectBundle\Entity\Team $team
     * @return Project
     */
    public function addTeam(\Jordo\ProjectBundle\Entity\Team $team)
    {
        $this->team[] = $team;
    
        return $this;
    }

    /**
     * Remove team
     *
     * @param Jordo\ProjectBundle\Entity\Team $team
     */
    public function removeTeam(\Jordo\ProjectBundle\Entity\Team $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get team
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
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

    /**
     * Set contact
     *
     * @param Jordo\ContactBundle\Entity\Contact $contact
     * @return Project
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

    /**
     * Add documents
     *
     * @param Jordo\ProjectBundle\Entity\Document $documents
     * @return Project
     */
    public function addDocument(\Jordo\ProjectBundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;
    
        return $this;
    }

    /**
     * Remove documents
     *
     * @param Jordo\ProjectBundle\Entity\Document $documents
     */
    public function removeDocument(\Jordo\ProjectBundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Set gantt
     *
     * @param Jordo\GanttBundle\Entity\Gantt $gantt
     * @return Project
     */
    public function setGantt(\Jordo\GanttBundle\Entity\Gantt $gantt = null)
    {
        $this->gantt = $gantt;
    
        return $this;
    }

    /**
     * Get gantt
     *
     * @return Jordo\GanttBundle\Entity\Gantt 
     */
    public function getGantt()
    {
        return $this->gantt;
    }

    /**
     * Set contexte
     *
     * @param string $contexte
     * @return Project
     */
    public function setContexte($contexte)
    {
        $this->contexte = $contexte;
    
        return $this;
    }

    /**
     * Get contexte
     *
     * @return string 
     */
    public function getContexte()
    {
        return $this->contexte;
    }

    /**
     * Set demande
     *
     * @param string $demande
     * @return Project
     */
    public function setDemande($demande)
    {
        $this->demande = $demande;
    
        return $this;
    }

    /**
     * Get demande
     *
     * @return string 
     */
    public function getDemande()
    {
        return $this->demande;
    }

    /**
     * Add states
     *
     * @param Jordo\ProjectBundle\Entity\State $states
     * @return Project
     */
    public function addState(\Jordo\ProjectBundle\Entity\State $states)
    {
        $this->states[] = $states;
    
        return $this;
    }

    /**
     * Remove states
     *
     * @param Jordo\ProjectBundle\Entity\State $states
     */
    public function removeState(\Jordo\ProjectBundle\Entity\State $states)
    {
        $this->states->removeElement($states);
    }

    /**
     * Get states
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getStates()
    {
        return $this->states;
    }
}