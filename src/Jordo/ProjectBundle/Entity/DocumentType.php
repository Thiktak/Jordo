<?php

namespace Jordo\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ProjectBundle\Entity\DocumentType
 *
 * @ORM\Table(name="project_document_type")
 * @ORM\Entity
 */
class DocumentType
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var boolean $isNumberofIntervenants
     *
     * @ORM\Column(name="is_numberof_intervenants", type="boolean")
     */
    private $isNumberofIntervenants;

    /**
     * @var integer $weight
     *
     * @ORM\Column(name="weight", type="smallint")
     */
    private $weight;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


    public function __toString()
    {
        return (string) $this->title;
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
     * Set title
     *
     * @param string $title
     * @return DocumentType
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
     * Set isNumberofIntervenants
     *
     * @param boolean $isNumberofIntervenants
     * @return DocumentType
     */
    public function setIsNumberofIntervenants($isNumberofIntervenants)
    {
        $this->isNumberofIntervenants = $isNumberofIntervenants;
    
        return $this;
    }

    /**
     * Get isNumberofIntervenants
     *
     * @return boolean 
     */
    public function getIsNumberofIntervenants()
    {
        return $this->isNumberofIntervenants;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return DocumentType
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return DocumentType
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
}
