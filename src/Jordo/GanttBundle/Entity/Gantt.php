<?php

namespace Jordo\GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\GanttBundle\Entity\Gantt
 *
 * @ORM\Table(name="gantt")
 * @ORM\Entity
 */
class Gantt
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime $dateStart
     *
     * @ORM\Column(name="date_start", type="datetime", nullable=true)
     */
    private $dateStart;

    /**
     * @ORM\OneToMany(targetEntity="\Jordo\GanttBundle\Entity\Phase", mappedBy="gantt")
     * @ORM\OrderBy({"iorder"="ASC"})
     */
    private $phases;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        //$this->dateStart   = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getBudget()
    {
        $budget = 0;
        
        foreach( $this->getPhases() as $phase )
            $budget += $phase->getNumberJeh() * $phase->getPrice();

        return $budget;
    }


    public function getNumberJEH()
    {
        $JEH = 0;
        
        foreach( $this->getPhases() as $phase )
            $JEH += $phase->getNumberJeh();

        return $JEH;
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Gantt
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
     * @return Gantt
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
     * Add phases
     *
     * @param Jordo\GanttBundle\Entity\Phase $phases
     * @return Gantt
     */
    public function addPhase(\Jordo\GanttBundle\Entity\Phase $phases)
    {
        $this->phases[] = $phases;
    
        return $this;
    }

    /**
     * Remove phases
     *
     * @param Jordo\GanttBundle\Entity\Phase $phases
     */
    public function removePhase(\Jordo\GanttBundle\Entity\Phase $phases)
    {
        $this->phases->removeElement($phases);
    }

    /**
     * Get phases
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Gantt
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