<?php

namespace Jordo\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\UserBundle\Entity\Subscription
 *
 * @ORM\Table(name="user_subscription")
 * @ORM\Entity
 */
class Subscription
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
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\SubscriptionDocType", inversedBy="type")
     */
    private $type;

    /**
     * @var integer $state
     *
     * @ORM\Column(name="state", type="string", length=20)
     */
    private $state;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="smallint")
     */
    private $year;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="subscriptions")
     */
    protected $user;


    public function __toString()
    {
        return (string) $this->state;
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
     * Set state
     *
     * @param integer $state
     * @return Subscription
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
        return $this->state;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Subscription
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
     * Set comment
     *
     * @param string $comment
     * @return Subscription
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
     * Constructor
     */
    public function __construct()
    {
        $this->type = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dateCreated = $this->dateCreated ?: new \DateTime();
    }
    
    /**
     * Add type
     *
     * @param Jordo\UserBundle\Entity\SubscriptionDocType $type
     * @return Subscription
     */
    public function addType(\Jordo\UserBundle\Entity\SubscriptionDocType $type)
    {
        $this->type[] = $type;
    
        return $this;
    }

    /**
     * Remove type
     *
     * @param Jordo\UserBundle\Entity\SubscriptionDocType $type
     */
    public function removeType(\Jordo\UserBundle\Entity\SubscriptionDocType $type)
    {
        $this->type->removeElement($type);
    }

    /**
     * Get type
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param Jordo\UserBundle\Entity\SubscriptionDocType $type
     * @return Subscription
     */
    public function setType(\Jordo\UserBundle\Entity\SubscriptionDocType $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return User
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
     * Set year
     *
     * @param integer $year
     * @return Subscription
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return year 
     */
    public function getYear()
    {
        return $this->year;
    }
}