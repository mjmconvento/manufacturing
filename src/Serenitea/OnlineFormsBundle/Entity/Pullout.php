<?php

namespace Serenitea\OnlineFormsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\InventoryBundle\Template\Entity\HasWarehouse;


use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="ser_pullout")
 */
class Pullout
{
    const STATUS_DRAFT = 'Draft';
    const STATUS_SENT = 'Sent';
    
    use HasGeneratedID;
    use HasCode;
    use HasWarehouse;
    use TrackCreate;
    
    /** @ORM\Column(type="string", length=200, nullable=true) */
    protected $to_field;
    
    /** @ORM\Column(type="string", length=200, nullable=true) */
    protected $cc_field; 
    
    /** @ORM\Column(type="string", length=80, nullable=true) */
    protected $status; 
    
    /**
     * @ORM\OneToMany(targetEntity="PulloutEntry", mappedBy="pullout", cascade={"persist"})
     */
    protected $entries;
    
    public function __construct()
    {
        $this->initTrackCreate();
        $this->entries = new ArrayCollection();
        $this->status = self::STATUS_DRAFT;
    }
    
    public function setTo($to){
        $this->to_field = $to;
        return $this;
    }
    
    public function setCc($cc){
        $this->cc_field = $cc;
        return $this;
    }
    
    public function setStatus($status){
        $this->status = $status;
        return $this;
    }
    
    public function addEntry(PulloutEntry $entry)
    {
        $this->entries->add($entry);
        $entry->setPullout($this);
        return $this;
    }
    
    public function clearEntries()
    {
        $this->entries->clear();
        return $this;
    }
    
    public function getEntries()
    {
        return $this->entries;
    }
    
    public function getCc()
    {
        return $this->cc_field;
    }
    
    public function getTo()
    {
        return $this->to_field;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function generateCode()
    {
        $this->code = 'PU-'.str_pad($this->id, 7,'0',STR_PAD_LEFT);
    }
    
    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasWarehouse($data);
        $this->dataHasCode($data);
        
        $data->to = $this->to_field;
        $data->cc = $this->cc_field;
        $data->status = $this->status;

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
