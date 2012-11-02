<?php

namespace Jordo\GanttBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\GanttBundle\Entity\Phase
 *
 * @ORM\Table(name="gantt_phase")
 * @ORM\Entity
 */
class Phase
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
     * @var integer $title
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var integer $iorder
     *
     * @ORM\Column(name="iorder", type="smallint")
     */
    private $iorder;

    /**
     * @var integer $numberDaysAfter
     *
     * @ORM\Column(name="number_days_after", type="smallint")
     */
    private $numberDaysAfter;

    /**
     * @var integer $numberDays
     *
     * @ORM\Column(name="number_days", type="smallint")
     */
    private $numberDays;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float $price
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var integer $numberJeh
     *
     * @ORM\Column(name="number_jeh", type="smallint")
     */
    private $numberJeh;

    /**
     * @ORM\ManyToOne(targetEntity="\Jordo\GanttBundle\Entity\Gantt", inversedBy="phases")
     */
    private $gantt;


    public function __toString()
    {
        return $this->title;
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
     * @return Phase
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
     * Set iorder
     *
     * @param integer $iorder
     * @return Phase
     */
    public function setIorder($iorder)
    {
        $this->iorder = $iorder;
    
        return $this;
    }

    /**
     * Get iorder
     *
     * @return integer 
     */
    public function getIorder()
    {
        return $this->iorder;
    }

    /**
     * Set numberDaysAfter
     *
     * @param integer $numberDaysAfter
     * @return Phase
     */
    public function setNumberDaysAfter($numberDaysAfter)
    {
        $this->numberDaysAfter = $numberDaysAfter;
    
        return $this;
    }

    /**
     * Get numberDaysAfter
     *
     * @return integer 
     */
    public function getNumberDaysAfter()
    {
        return $this->numberDaysAfter;
    }

    /**
     * Set numberDays
     *
     * @param integer $numberDays
     * @return Phase
     */
    public function setNumberDays($numberDays)
    {
        $this->numberDays = $numberDays;
    
        return $this;
    }

    /**
     * Get numberDays
     *
     * @return integer 
     */
    public function getNumberDays()
    {
        return $this->numberDays;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Phase
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
     * Set price
     *
     * @param float $price
     * @return Phase
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set numberJeh
     *
     * @param integer $numberJeh
     * @return Phase
     */
    public function setNumberJeh($numberJeh)
    {
        $this->numberJeh = $numberJeh;
    
        return $this;
    }

    /**
     * Get numberJeh
     *
     * @return integer 
     */
    public function getNumberJeh()
    {
        return $this->numberJeh;
    }

    /**
     * Set gantt
     *
     * @param Jordo\GanttBundle\Entity\Gantt $gantt
     * @return Phase
     */
    public function setGantt(\Jordo\GanttBundle\Entity\Gantt $gantt = null)
    {
        $this->gantt = $gantt;
    
        return $this;
    }

    /**
     * Get gantt
     *
     * @return Jordo\GanttBundle\Entity\Gantt 
     */
    public function getGantt()
    {
        return $this->gantt;
    }
}