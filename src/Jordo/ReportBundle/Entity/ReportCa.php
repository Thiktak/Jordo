<?php

namespace Jordo\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jordo\CalendarBundle\Entity\Event as Event;

/**
 * Jordo\ReportBundle\Entity\ReportCa
 *
 * @ORM\Table(name="report_ca")
 * @ORM\Entity
 */
class ReportCa extends Event
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
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    protected $type;

    /**
     * @var string $reportCaItems
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ReportBundle\Entity\ReportItem", mappedBy="reportCa")
     */
    protected $reportCaItems;
    /**
     * @var \DateTime $dateCreated
     */
    protected $dateCreated;

    /**
     * @var \DateTime $dateUpdated
     */
    protected $dateUpdated;

    /**
     * @var \DateTime $dateStart
     */
    protected $dateStart;

    /**
     * @var \DateTime $dateEnd
     */
    protected $dateEnd;

    /**
     * @var string $title
     */
    protected $title;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var boolean $isTodo
     */
    protected $isTodo;

    /**
     * @var Jordo\UserBundle\Entity\User
     */
    protected $addedBy;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $guests;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reportCaItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guests = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct();
    }
    
    public function __toString()
    {
        return (string) '[RCa] ' . $this->title;
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
     * @return ReportCa
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return ReportCa
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
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return ReportCa
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return ReportCa
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
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return ReportCa
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    
        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ReportCa
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
     * Set description
     *
     * @param string $description
     * @return ReportCa
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
     * Set isTodo
     *
     * @param boolean $isTodo
     * @return ReportCa
     */
    public function setIsTodo($isTodo)
    {
        $this->isTodo = $isTodo;
    
        return $this;
    }

    /**
     * Get isTodo
     *
     * @return boolean 
     */
    public function getIsTodo()
    {
        return $this->isTodo;
    }

    /**
     * Add reportCaItems
     *
     * @param Jordo\ReportBundle\Entity\ReportItem $reportCaItems
     * @return ReportCa
     */
    public function addReportCaItem(\Jordo\ReportBundle\Entity\ReportItem $reportCaItems)
    {
        $this->reportCaItems[] = $reportCaItems;
    
        return $this;
    }

    /**
     * Remove reportCaItems
     *
     * @param Jordo\ReportBundle\Entity\ReportItem $reportCaItems
     */
    public function removeReportCaItem(\Jordo\ReportBundle\Entity\ReportItem $reportCaItems)
    {
        $this->reportCaItems->removeElement($reportCaItems);
    }

    /**
     * Get reportCaItems
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReportCaItems()
    {
        return $this->reportCaItems;
    }

    /**
     * Set addedBy
     *
     * @param Jordo\UserBundle\Entity\User $addedBy
     * @return ReportCa
     */
    public function setAddedBy(\Jordo\UserBundle\Entity\User $addedBy = null)
    {
        $this->addedBy = $addedBy;
    
        return $this;
    }

    /**
     * Get addedBy
     *
     * @return Jordo\UserBundle\Entity\User 
     */
    public function getAddedBy()
    {
        return $this->addedBy;
    }

    /**
     * Add guests
     *
     * @param Jordo\CalendarBundle\Entity\Guest $guests
     * @return Event
     */
    public function addGuest(\Jordo\CalendarBundle\Entity\Guest $guests)
    {
        $this->guests[] = $guests;
    
        return $this;
    }

    /**
     * Remove guests
     *
     * @param Jordo\CalendarBundle\Entity\Guest $guests
     */
    public function removeGuest(\Jordo\CalendarBundle\Entity\Guest $guests)
    {
        $this->guests->removeElement($guests);
    }

    /**
     * Get guests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGuests()
    {
        return $this->guests;
    }
}