<?php

namespace Jordo\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ProjectBundle\Entity\Document
 *
 * @ORM\Table(name="project_document")
 * @ORM\Entity
 */
class Document
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
     * @var \DateTime $dateCreated
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\UserBundle\Entity\User", inversedBy="user")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\ProjectBundle\Entity\DocumentType", inversedBy="doc")
     */
    protected $doctype;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\DocumentBundle\Entity\Document", inversedBy="document")
     */
    protected $document;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\ProjectBundle\Entity\Project", inversedBy="documents")
     */
    protected $project;


    public function __construct()
    {
        $this->dateCreated = $this->dateCreated ?: new \DateTime();
    }

    public function __toString()
    {
        return (string) $this->getDocument()->getTitle();
    }

    public function getState()
    {
        /*//$list = array('ok', 'progress', 'warning', 'error');

        foreach( $this->getComments() as $comment )
            if( $comment->getState() )
                return $comment->getState();

        return null;*/
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
     * @return Document
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
     * Set user
     *
     * @param Jordo\UserBundle\Entity\User $user
     * @return Document
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
     * Set doctype
     *
     * @param Jordo\ProjectBundle\Entity\DocumentType $doctype
     * @return Document
     */
    public function setDoctype(\Jordo\ProjectBundle\Entity\DocumentType $doctype = null)
    {
        $this->doctype = $doctype;
    
        return $this;
    }

    /**
     * Get doctype
     *
     * @return Jordo\ProjectBundle\Entity\DocumentType 
     */
    public function getDoctype()
    {
        return $this->doctype;
    }

    /**
     * Set project
     *
     * @param Jordo\ProjectBundle\Entity\Project $project
     * @return Document
     */
    public function setProject(\Jordo\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return Jordo\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set document
     *
     * @param Jordo\DocumentBundle\Entity\Document $document
     * @return Document
     */
    public function setDocument(\Jordo\DocumentBundle\Entity\Document $document = null)
    {
        $this->document = $document;
    
        return $this;
    }

    /**
     * Get document
     *
     * @return Jordo\DocumentBundle\Entity\Document 
     */
    public function getDocument()
    {
        return $this->document;
    }
}