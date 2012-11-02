<?php

namespace Jordo\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\CalendarBundle\Entity\Event
 *
 * @ORM\Table(name="calendar_event")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"event" = "Event", "call" = "\Jordo\ContactBundle\Entity\Call", "reportCa" = "\Jordo\ReportBundle\Entity\ReportCa"})
 */
class Event
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
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var \DateTime $dateUpdated
     *
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    protected $dateUpdated;

    /**
     * @var \DateTime $dateStart
     *
     * @ORM\Column(name="date_start", type="datetime")
     */
    protected $dateStart;

    /**
     * @var \DateTime $dateEnd
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     */
    protected $dateEnd;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var boolean $isTodo
     *
     * @ORM\Column(name="is_todo", type="boolean", nullable=true)
     */
    protected $isTodo;

    /**
     * @var boolean $isOpen
     *
     * @ORM\Column(name="is_open", type="boolean", nullable=true)
     */
    protected $isOpen;

    /**
     * @var User $added_by
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="user")
     */
    protected $addedBy;

    /**
     * @var User $guests
     *
     * @ORM\OneToMany(targetEntity="\Jordo\CalendarBundle\Entity\Guest", mappedBy="event")
     */
    protected $guests;

    /* @ORM\JoinTable(name="calendar_guest_user",
     *   joinColumns={@ORM\JoinColumn(name="guest_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )*/


    public function __construct()
    {
        $this->dateCreated = $this->dateCreated ?: new \DateTime();
        $this->dateStart = new \DateTime();
    }

    public function __toString()
    {
        return (string) $this->title ?: $this->id;
    }

    public function isIntoGuests($user)
    {
        foreach( $this->getGuests() as $guest )
            if( $guest->getUser() == $user )
                return $guest;
        return false;
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

    public function getDiscr()
    {
        return $this->discr;
    }
    public function setDiscr($discr)
    {
        $this->discr = $discr;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * Set addedBy
     *
     * @param Jordo\UserBundle\Entity\User $addedBy
     * @return Event
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
     * Set isOpen
     *
     * @param boolean $isOpen
     * @return Event
     */
    public function setIsOpen($isOpen)
    {
        $this->isOpen = $isOpen;
    
        return $this;
    }

    /**
     * Get isOpen
     *
     * @return boolean 
     */
    public function getIsOpen()
    {
        return $this->isOpen;
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


    /**
     * Get guests
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGuestsByResponse($response)
    {
        $guests = array();
        
        foreach( $this->getGuests() as $guest )
            if( $guest->getResponse() == $response )
                $guests[] = $guest;

        return $guests;
    }
}