<?php

namespace Jordo\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\CalendarBundle\Entity\Guest
 *
 * @ORM\Table(name="calendar_guest")
 * @ORM\Entity
 */
class Guest
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
     * @var User $added_by
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\CalendarBundle\Entity\Event", inversedBy="guests")
     */
    protected $event;

    /**
     * @var User $guests
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="event")
     */
    protected $user;

    /**
     * @var integer $response
     *
     * @ORM\Column(name="response", type="smallint", nullable=true)
     */
    protected $response;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }

    public function __toString()
    {
        return (string) sprintf("%s (%s)", $this->getUser(), $this->getResponse());
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Guest
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
     * @return Guest
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
     * Set response
     *
     * @param integer $response
     * @return Guest
     */
    public function setResponse($response)
    {
        $this->response = $response;
    
        return $this;
    }

    /**
     * Get response
     *
     * @return integer 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set event
     *
     * @param Jordo\CalendarBundle\Entity\Event $event
     * @return Guest
     */
    public function setEvent(\Jordo\CalendarBundle\Entity\Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return Jordo\CalendarBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return Guest
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