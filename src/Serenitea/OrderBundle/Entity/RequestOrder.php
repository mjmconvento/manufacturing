<?php

namespace Serenitea\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\UserBundle\Entity\User;
use Catalyst\InventoryBundle\Entity\Warehouse;
use DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="req_order")
*
*/
class RequestOrder
{
	use HasGeneratedID;
	use TrackCreate;

	const STATUS_DRAFT      = 'Draft';
    const STATUS_APPROVED   = 'Approved';
    const STATUS_SENT       = 'Sent';
    const STATUS_CANCEL     = 'Cancelled';
    const STATUS_FULFILLED  = 'Fulfilled';

    /** @ORM\Column(type="string", length=40, nullable=true) */
    protected $code;

    /** @ORM\Column(type="date") */
    protected $date_needed;

    /** @ORM\Column(type="string", length=30) */
    protected $status_id;

    /** @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Warehouse")
    * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
    */
    protected $warehouse;

    /** @ORM\Column(type="date") */
    protected $date_issue;

    /**
     * @ORM\OneToMany(targetEntity="ROEntry", mappedBy="request_order", cascade={"persist"})
     */
    protected $entries;

    /**
     * @ORM\OneToMany(targetEntity="RODelivery", mappedBy="request_order", cascade={"persist"})
     */
    protected $deliveries;

    public function __construct()
    {
        $this->initTrackCreate();
        $this->status_id = self::STATUS_DRAFT;
        $this->deliveries = new ArrayCollection();
        $this->entries = new ArrayCollection();
    }

    // setters
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setDateIssue(DateTime $date)
    {
        $this->date_issue = $date;
        return $this;
    }

    public function setDateNeeded(DateTime $date)
    {
        $this->date_needed = $date;
        return $this;
    }

    public function setWarehouse(Warehouse $wh)
    {
        $this->warehouse = $wh;
        $this->type_id = $wh->getID();
        return $this;
    }

    public function addEntry(ROEntry $entry)
    {
        // set reqyest order
        $entry->setRequestOrder($this);

        // add entry
        $this->entries->add($entry);
        return $this;
    }    

    public function clearEntries()
    {
        $this->entries->clear();
        return $this;
    }

    public function addDelivery(RODelivery $del)
    {
        $del->setRequestOrder($this);

        $this->deliveries->add($del);

        return $this;
    }

    public function clearDeliveries()
    {
        $this->deliveries->clear();
        return $this;
    }

    public function setStatus($status)
    {
        // TODO: check for invalid status
        $this->status_id = $status;
        return $this;
    }    

    // getters
    public function getID()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getDeliveries()
    {
        return $this->deliveries;
    }

    public function getStatus()
    {
        return $this->status_id;
    }

    public function getStatusFormatted()
    {
        return ucfirst($this->status_id);
    }

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getDateNeeded()
    {
        return $this->date_needed;
    }
    
    public function generateCode()
    {
        $year = date('Y');
        $this->code = "PO-".$year.'-'.str_pad($this->id,5, "0", STR_PAD_LEFT);
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

    // utils
    public function canDeliver()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        if ($this->status_id == self::STATUS_FULFILLED)
            return true;

        //check if the user is admin
        $user = $this->getUserCreate();        
        if($user == 'admin')
            return true;

        return false;
    }

    public function canApprove()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        return false;
    }

    public function canSend()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        if ($this->status_id == self::STATUS_APPROVED)
            return true;

        return false;
    }
    
    public function canCancel()
    {
        if ($this->status_id == self::STATUS_DRAFT)
            return true;

        if ($this->status_id == self::STATUS_APPROVED)
            return true;

        return false;
    }

    public function canModifyEntries()
    {
        if (strtolower($this->status_id) == strtolower(self::STATUS_DRAFT))
            return true;
        //check if the user is admin
        $user = $this->getUserCreate()->getUsername();
        if($user == 'admin')
            return true;

        return false;
    }

    public function canFulfill()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        return false;
    }

    public function canAddDelivery()
    {
        if ($this->status_id == self::STATUS_SENT)
            return true;

        return false;
    }

    public function toData()
    {
        $data = new \stdClass();

        $data->code = $this->code;
        $data->date_needed = $this->date_needed;       
        $data->status_id = $this->status_id;
        $data->date_issue = $this->date_issue;        
        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $data->warehouse_id = $this->getWarehouse()->getID();        

        $entries = array();
        foreach ($this->entries as $entry){
            $entries[] = $entry->toData();
        }
        $data->entries = $entries;

        return $data;
    }
}