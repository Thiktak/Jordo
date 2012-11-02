<?php

namespace Thiktak\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thiktak\CoreBundle\Entity\Notify
 *
 * @ORM\Table(name="notify")
 * @ORM\Entity
 */
class Notify
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    // @TODO Thiktak\UserBundle\Entoty\User
    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="user")
     */
    protected $user;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var string $content
     *
     * @ORM\Column(name="object", type="string", length=255)
     */
    protected $object;

    /**
     * @var string $param1
     *
     * @ORM\Column(name="param1", type="string", length=255, nullable=true)
     */
    protected $param1;

    /**
     * @var string $param2
     *
     * @ORM\Column(name="param2", type="string", length=255, nullable=true)
     */
    protected $param2;

    /**
     * @var string $param3
     *
     * @ORM\Column(name="param3", type="string", length=255, nullable=true)
     */
    protected $param3;

    /**
     * @var string $param4
     *
     * @ORM\Column(name="param4", type="string", length=255, nullable=true)
     */
    protected $param4;


    public function __construct()
    {
        $this->dateCreated = new \DateTime();
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
     * @return Notify
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
     * Set object
     *
     * @param string $object
     * @return Notify
     */
    public function setObject($object)
    {
        $this->object = $object;
    
        return $this;
    }

    /**
     * Get object
     *
     * @return string 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set param1
     *
     * @param string $param1
     * @return Notify
     */
    public function setParam1($param1)
    {
        $this->param1 = $param1;
    
        return $this;
    }

    /**
     * Get param1
     *
     * @return string 
     */
    public function getParam1()
    {
        return $this->param1;
    }

    /**
     * Set param2
     *
     * @param string $param2
     * @return Notify
     */
    public function setParam2($param2)
    {
        $this->param2 = $param2;
    
        return $this;
    }

    /**
     * Get param2
     *
     * @return string 
     */
    public function getParam2()
    {
        return $this->param2;
    }

    /**
     * Set param3
     *
     * @param string $param3
     * @return Notify
     */
    public function setParam3($param3)
    {
        $this->param3 = $param3;
    
        return $this;
    }

    /**
     * Get param3
     *
     * @return string 
     */
    public function getParam3()
    {
        return $this->param3;
    }

    /**
     * Set param4
     *
     * @param string $param4
     * @return Notify
     */
    public function setParam4($param4)
    {
        $this->param4 = $param4;
    
        return $this;
    }

    /**
     * Get param4
     *
     * @return string 
     */
    public function getParam4()
    {
        return $this->param4;
    }

    /**
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return Notify
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