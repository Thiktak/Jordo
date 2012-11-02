<?php

namespace Jordo\UserBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Jordo\UserBundle\Entity\User", mappedBy="groups")
     * @ORM\OrderBy({"fname"="DESC"})
     */
    protected $users;

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function __construct($name = null, $roles = array())
    {
        parent::__construct($name, (array) $roles);
    }

    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
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
     * Add users
     *
     * @param Jordo\UserBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\Jordo\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param Jordo\UserBundle\Entity\User $users
     */
    public function removeUser(\Jordo\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}