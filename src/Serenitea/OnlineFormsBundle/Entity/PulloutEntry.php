<?php

namespace Serenitea\OnlineFormsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasNotes;
use Catalyst\CoreBundle\Template\Entity\HasPrice;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="ser_pullout_entry")
 */
class PulloutEntry
{
    use HasGeneratedID;
    use HasQuantity;
    use HasNotes;
    use HasPrice;
    
    /**
     * @ORM\ManyToOne(targetEntity="Pullout")
     * @ORM\JoinColumn(name="pullout_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $pullout;
    
    /** @ORM\Column(type="datetime") */
    protected $report_date;
    
     /** @ORM\Column(type="string", length=200, nullable=true) */
    protected $item;
    


    public function __construct()
    {
        $this->initHasQuantity();
    }
    
    public function setReportDate(DateTime $date)
    {
        $this->report_date = $date;
        return $this;
    }

    public function setPullout(Pullout $pullout)
    {
        $this->pullout = $pullout;
        return $this;
    }
    
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }
    
    public function getReportDate()
    {
        $this->report_date;
    }

    public function getPullout()
    {
        return $this->pullout;
    }
    
    public function getTotalPrice()
    {
        return $this->quantity * $this->price;
    }
    
    public function getItem()
    {
        return $this->item;
    }
    

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasQuantity($data);
        $this->dataHasNotes($data);
        $this->dataHasPrice($data);
        $data->item = $this->item;
        $data->report_date = $this->report_date->format('Y-m-d H:i:s');

        return $data;
    }
}
