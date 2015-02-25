<?php

namespace Serenitea\OnlineFormsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasNotes;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="ser_repair_entry")
 */
class RepairEntry
{
    use HasGeneratedID;
    use HasNotes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Pullout")
     * @ORM\JoinColumn(name="repair_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $repair;
    
    /** @ORM\Column(type="datetime") */
    protected $report_date;
    
     /** @ORM\Column(type="string", length=200, nullable=true) */
    protected $item;
    
     /** @ORM\Column(type="string", nullable=true) */
    protected $findings;
    


    
    public function setReportDate(DateTime $date)
    {
        $this->report_date = $date;
        return $this;
    }

    public function setRepair(Repair $repair)
    {
        $this->repair = $repair;
        return $this;
    }
    
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }
    
    public function setFindings($findings)
    {
        $this->findings = $findings;
        return $this;
    }
    
    public function getReportDate()
    {
        $this->report_date;
    }

    public function getRepair()
    {
        return $this->repair;
    }
    
    
    public function getItem()
    {
        return $this->item;
    }
    
    public function getFindings()
    {
        return $this->findings;
    }
    

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasNotes($data);
        $data->item = $this->item;
        $data->findings = $this->findings;
        $data->report_date = $this->report_date->format('Y-m-d H:i:s');

        return $data;
    }
}
