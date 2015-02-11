<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasNotes;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_request")
 */
class PurchaseRequest
{
    use HasGeneratedID;
    use TrackCreate;
    use HasNotes;

    const STATUS_APPROVED = 'Approved';
    const STATUS_DRAFT = 'Draft';

    /** @ORM\Column(type="string", length=40, nullable=false) */
    protected $code;

    /** @ORM\Column(type="date") */
    protected $date_needed;

    /** @ORM\Column(type="string", length=30) */
    protected $status_id;

    /**
     * @ORM\OneToMany(targetEntity="PREntry", mappedBy="purchase_request", cascade={"persist"})
     */
    protected $entries;
    
    
    /** @ORM\Column(type="string") */
    protected $purpose;
    
    /** @ORM\Column(type="string", length=30) */
    protected $reference_num;
    

    // @TODO: Link with HR department;
    /** @ORM\Column(type="string", length=30) */
    protected $department;
    

    public function __construct()
    {
        $this->initTrackCreate();
        $this->status_id = self::STATUS_DRAFT;
    }

    // setters
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setDateNeeded(DateTime $date)
    {
        $this->date_needed = $date;
        return $this;
    }
    
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
        return $this;
    }
    
    public function setReferenceNum($reference)
    {
        $this->reference_num = $reference;
        return $this;
    }
    
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    public function addEntry(PREntry $entry)
    {
        // set purchase order
        $entry->setPurchaseRequest($this);

        // add entry
        $this->entries->add($entry);
        return $this;
    }

    public function clearEntries()
    {
        $this->entries->clear();
        return $this;
    }



    public function setStatus($status)
    {
        // TODO: check for invalid status
        $this->status_id = $status;
        return $this;
    }

    // getters
    public function getCode()
    {
        return $this->code;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getStatus()
    {
        return $this->status_id;
    }

    public function getStatusFormatted()
    {
        return ucfirst($this->status_id);
    }
    
    public function getPurpose()
    {
        return $this->purpose;
    }
    
    public function getReferenceNum()
    {
        return $this->reference_num;
    }
    
    public function getDepartment()
    {
        return $this->department;
    }
    
    public function getDateNeeded()
    {
        return $this->date_needed;
    }


    public function toData()
    {
        $data = new \stdClass();

        $data->code = $this->code;
        $data->date_needed = $this->date_needed;
        $data->purpose = $this->purpose;
        $data->status_id = $this->status_id;
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasNotes($data);

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}