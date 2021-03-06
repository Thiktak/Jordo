<?php

namespace Jordo\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ContactBundle\Entity\Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity
 */
class Contact
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
     * @var string $fname
     *
     * @ORM\Column(name="fname", type="string", length=255)
     */
    protected $fname;

    /**
     * @var string $lname
     *
     * @ORM\Column(name="lname", type="string", length=255)
     */
    protected $lname;

    /**
     * @var string $firm
     *
     * @ORM\Column(name="firm", type="string", length=255)
     */
    protected $firm;

    /**
     * @var string $addr
     *
     * @ORM\Column(name="addr", type="text")
     */
    protected $addr;

    /**
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $dateCreated;

    /**
     * @var \DateTime $dateUpdated
     *
     * @ORM\Column(name="date_updated", type="datetime")
     */
    protected $dateUpdated;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\ContactBundle\Entity\Type", inversedBy="contact")
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="\Jordo\ContactBundle\Entity\Info", mappedBy="contact")
     */
    protected $infos;

    /**
     * @var $projects
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ProjectBundle\Entity\Project", mappedBy="contact")
     */
    protected $projects;


    public function __construct()
    {
        $this->dateCreated = $this->dateCreated ?: new \DateTime();
        $this->dateUpdated = new \DateTime();
    }

    public function __toString()
    {
        return (string) ucwords(trim(sprintf('%s %s', $this->lname, $this->fname)));
    }

    public function getCalls()
    {
        $calls = array();
        $i = 0;

        foreach( $this->infos as $info )
            foreach( $info->getCalls() as $call )
                $calls[$call->getDateStart()->format('U') . '-' . $i++] = $call;
                
        krsort($calls);
        return array_values($calls);
    }

    public function hasProjects()
    {
        return count($this->projects);
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
     * Set fname
     *
     * @param string $fname
     * @return Contact
     */
    public function setFname($fname)
    {
        $this->fname = $fname;
    
        return $this;
    }

    /**
     * Get fname
     *
     * @return string 
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Set lname
     *
     * @param string $lname
     * @return Contact
     */
    public function setLname($lname)
    {
        $this->lname = $lname;
    
        return $this;
    }

    /**
     * Get lname
     *
     * @return string 
     */
    public function getLname()
    {
        return $this->lname;
    }

    /**
     * Set firm
     *
     * @param string $firm
     * @return Contact
     */
    public function setFirm($firm)
    {
        $this->firm = $firm;
    
        return $this;
    }

    /**
     * Get firm
     *
     * @return string 
     */
    public function getFirm()
    {
        return $this->firm;
    }

    /**
     * Set addr
     *
     * @param string $addr
     * @return Contact
     */
    public function setAddr($addr)
    {
        $this->addr = $addr;
    
        return $this;
    }

    /**
     * Get addr
     *
     * @return string 
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Contact
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
     * @return Contact
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
     * Set type
     *
     * @param Jordo\ContactBundle\Entity\Type $type
     * @return Contact
     */
    public function setType(\Jordo\ContactBundle\Entity\Type $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return Jordo\ContactBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add infos
     *
     * @param Jordo\ContactBundle\Entity\Info $infos
     * @return Contact
     */
    public function addInfo(\Jordo\ContactBundle\Entity\Info $infos)
    {
        $this->infos[] = $infos;
    
        return $this;
    }

    /**
     * Remove infos
     *
     * @param Jordo\ContactBundle\Entity\Info $infos
     */
    public function removeInfo(\Jordo\ContactBundle\Entity\Info $infos)
    {
        $this->infos->removeElement($infos);
    }

    /**
     * Get infos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Add projects
     *
     * @param Jordo\ProjectBundle\Entity\Project $projects
     * @return Contact
     */
    public function addProject(\Jordo\ProjectBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;
    
        return $this;
    }

    /**
     * Remove projects
     *
     * @param Jordo\ProjectBundle\Entity\Project $projects
     */
    public function removeProject(\Jordo\ProjectBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }
}