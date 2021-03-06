<?php

namespace Jordo\UserBundle\Entity;


use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupableInterface;

/**
 * @ORM\Entity(repositoryClass="Jordo\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $lname
     *
     * @ORM\Column(name="lname", type="string", nullable=true)
     */
    protected $lname;

    /**
     * @var string $fname
     *
     * @ORM\Column(name="fname", type="string", nullable=true)
     */
    protected $fname;

    /**
     * @var integer $promo
     *
     * @ORM\Column(name="promo", type="smallint", nullable=true)
     */
    protected $promo;

    /**
     * @var string $filiere
     *
     * @ORM\Column(name="filiere", type="string", length=50, nullable=true)
     */
    protected $filiere;

    /**
     * @var string $socialNumber
     *
     * @ORM\Column(name="social_number", type="string", length=15, nullable=true)
     */
    protected $socialNumber;

    /**
     * @var string $studentNumber
     *
     * @ORM\Column(name="student_number", type="string", length=15, nullable=true)
     */
    protected $studentNumber;

    /**
     * @var string $addrPerso
     *
     * @ORM\Column(name="addr_perso", type="text", nullable=true)
     */
    protected $addrPerso;

    /**
     * @var string $addrParents
     *
     * @ORM\Column(name="addr_parents", type="text", nullable=true)
     */
    protected $addrParents;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    protected $phone;

    /**
     * @var \DateTime $birth
     *
     * @ORM\Column(name="birth", type="date", nullable=true)
     */
    protected $birth;

    /**
     * @var string $birthPlace
     *
     * @ORM\Column(name="birth_place", type="text", nullable=true)
     */
    protected $birthPlace;

    /**
     * @var string $subscriptions
     *
     * @ORM\OneToMany(targetEntity="\Jordo\UserBundle\Entity\Subscription", mappedBy="user")
     */
    protected $subscriptions;

    /**
     * @ORM\ManyToMany(targetEntity="Jordo\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name"="DESC"})
     */
    protected $groups;

    /**
     * @var $teams
     *
     * @ORM\OneToMany(targetEntity="\Jordo\ProjectBundle\Entity\Team", mappedBy="user")
     */
    protected $teams;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    public function __toString()
    {
        if( empty($this->fname) && empty($this->lname) ) {
            list($this->fname, $this->lname) = explode('.', $this->username);
        }
        
        return (string) trim(sprintf('%s %s', ucfirst($this->lname), ucfirst($this->fname)));
    }

    public function hasSubscription($subscription, $year = null)
    {
        $subscription = is_object($subscription) ? $subscription->getId() : $subscription;

        foreach( $this->subscriptions as $sub )
            if( $sub->getType()->getId() == $subscription && $year == $sub->getYear() )
                return $sub;
        return null;
    }

    public function getYearsOfSubscription()
    {
        $years = array();
        
        foreach( $this->getSubscriptions() as $sub )
            $years[$sub->getYear()] = $sub->getYear();

        return $years;
    }

    public function getType($year = null)
    {
        $year = $year ?: (date('Y') + 1 + (date('m') > 8 ? -1 : 0));

        if( count($this->subscriptions) == 0 )
            return 'extern';

        foreach( $this->subscriptions as $sub )
            if( $year == $sub->getYear() )
                return 'member';

        return 'ancien';
    }

    public function hasGroup($group)
    {
        foreach( $this->getGroups() as $oGroup )
            if( $oGroup == $group OR (is_numeric($group) AND $oGroup->getId() == $group) )
                return $oGroup;
        
        return null;
    }

    public function isUpdated($subscription, $year)
    {
        $i = 0;

        foreach( (array) $subscription as $sub )
            if( $this->hasSubscription($sub, $year) == 'ok' )
                $i++;
        
        return $i;
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
     * Set lname
     *
     * @param string $lname
     * @return User
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
     * Set fname
     *
     * @param string $fname
     * @return User
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
     * Set socialNumber
     *
     * @param integer $socialNumber
     * @return User
     */
    public function setSocialNumber($socialNumber)
    {
        $this->socialNumber = $socialNumber;
    
        return $this;
    }

    /**
     * Get socialNumber
     *
     * @return integer 
     */
    public function getSocialNumber()
    {
        return $this->socialNumber;
    }

    /**
     * Set studentNumber
     *
     * @param integer $studentNumber
     * @return User
     */
    public function setStudentNumber($studentNumber)
    {
        $this->studentNumber = $studentNumber;
    
        return $this;
    }

    /**
     * Get studentNumber
     *
     * @return integer 
     */
    public function getStudentNumber()
    {
        return $this->studentNumber;
    }

    /**
     * Set addrPerso
     *
     * @param string $addrPerso
     * @return User
     */
    public function setAddrPerso($addrPerso)
    {
        $this->addrPerso = $addrPerso;
    
        return $this;
    }

    /**
     * Get addrPerso
     *
     * @return string 
     */
    public function getAddrPerso()
    {
        return $this->addrPerso;
    }

    /**
     * Set addrParents
     *
     * @param string $addrParents
     * @return User
     */
    public function setAddrParents($addrParents)
    {
        $this->addrParents = $addrParents;
    
        return $this;
    }

    /**
     * Get addrParents
     *
     * @return string 
     */
    public function getAddrParents()
    {
        return $this->addrParents;
    }

    /**
     * Set birth
     *
     * @param \DateTime $birth
     * @return User
     */
    public function setBirth($birth)
    {
        $this->birth = $birth;
    
        return $this;
    }

    /**
     * Get birth
     *
     * @return \DateTime 
     */
    public function getBirth()
    {
        return $this->birth;
    }

    /**
     * Set birthPlace
     *
     * @param string $birthPlace
     * @return User
     */
    public function setBirthPlace($birthPlace)
    {
        $this->birthPlace = $birthPlace;
    
        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return string 
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add subscriptions
     *
     * @param Jordo\UserBundle\Entity\Subscription $subscriptions
     * @return User
     */
    public function addSubscription(\Jordo\UserBundle\Entity\Subscription $subscriptions)
    {
        $this->subscriptions[] = $subscriptions;
    
        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @param Jordo\UserBundle\Entity\Subscription $subscriptions
     */
    public function removeSubscription(\Jordo\UserBundle\Entity\Subscription $subscriptions)
    {
        $this->subscriptions->removeElement($subscriptions);
    }

    /**
     * Get subscriptions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * Set promo
     *
     * @param integer $promo
     * @return User
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;
    
        return $this;
    }

    /**
     * Get promo
     *
     * @return integer 
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Set filiere
     *
     * @param string $filiere
     * @return User
     */
    public function setFiliere($filiere)
    {
        $this->filiere = $filiere;
    
        return $this;
    }

    /**
     * Get filiere
     *
     * @return string 
     */
    public function getFiliere()
    {
        return $this->filiere;
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add team
     *
     * @param Jordo\ProjectBundle\Entity\Team $team
     * @return User
     */
    public function addTeam(\Jordo\ProjectBundle\Entity\Team $team)
    {
        $this->team[] = $team;
    
        return $this;
    }

    /**
     * Remove team
     *
     * @param Jordo\ProjectBundle\Entity\User $team
     */
    public function removeTeam(\Jordo\ProjectBundle\Entity\Team $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get teams
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }
}