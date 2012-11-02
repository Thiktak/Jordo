<?php

namespace Jordo\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jordo\ReportBundle\Entity\ReportItem
 *
 * @ORM\Table(name="report_item")
 * @ORM\Entity
 */
class ReportItem
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
     * @var string $object
     *
     * @ORM\Column(name="object", type="string", length=255)
     */
    private $object;

    /**
     * @var integer $objectId
     *
     * @ORM\Column(name="object_id", type="integer")
     */
    private $objectId;

    /**
     * @var string $checked
     *
     * @ORM\Column(name="checked", type="string", length=50)
     */
    private $checked;

    /**
     * @var string $comment
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var string $reportCa
     *
     * @ORM\ManyToOne(targetEntity="\Jordo\ReportBundle\Entity\ReportCa", inversedBy="reportCaItems")
     */
    private $reportCa;


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
     * Set object
     *
     * @param string $object
     * @return ReportItem
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
     * Set objectId
     *
     * @param integer $objectId
     * @return ReportItem
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    
        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set checked
     *
     * @param string $checked
     * @return ReportItem
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;
    
        return $this;
    }

    /**
     * Get checked
     *
     * @return string 
     */
    public function getChecked()
    {
        return $this->checked;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ReportItem
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
     * Set reportCa
     *
     * @param Jordo\ReportBundle\Entity\ReportCa $reportCa
     * @return ReportItem
     */
    public function setReportCa(\Jordo\ReportBundle\Entity\ReportCa $reportCa = null)
    {
        $this->reportCa = $reportCa;
    
        return $this;
    }

    /**
     * Get reportCa
     *
     * @return Jordo\ReportBundle\Entity\ReportCa 
     */
    public function getReportCa()
    {
        return $this->reportCa;
    }
}