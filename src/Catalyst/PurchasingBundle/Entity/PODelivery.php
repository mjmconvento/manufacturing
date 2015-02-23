<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_delivery")
 */
class PODelivery
{
    use HasGeneratedID;
    use HasCode;
    use TrackCreate;

    const STATUS_DRAFT = 'Draft';
    const STATUS_RECEIVED = 'Received';
    /** @ORM\Column(type="date") */
    protected $date_deliver;

    /** @ORM\Column(type="string", length=80) */
    protected $external_code;
    
    /** @ORM\Column(type="string", length=20) */
    protected $status;
    
    /**
     * @ORM\ManyToOne(targetEntity="PurchaseOrder")
     * @ORM\JoinColumn(name="po_id", referencedColumnName="id")
     */
    protected $purchase_order;

    /**
     * @ORM\OneToMany(targetEntity="PODeliveryEntry", mappedBy="delivery", cascade={"persist"})
     */
    protected $entries;

    public function __construct()
    {
        $this->initTrackCreate();
        $this->date_delivery = new DateTime();
        $this->entries = new ArrayCollection();
        $this->status = self::STATUS_DRAFT;
    }


    public function setDateDeliver(DateTime $date)
    {
        $this->date_deliver = $date;
        return $this;
    }

    public function setPurchaseOrder(PurchaseOrder $po)
    {
        $this->purchase_order = $po;
        return $this;
    }
    
    public function setExternalCode($code)
    {
        $this->external_code = $code;
        return $this;
    }

    public function getExternalCode()
    {
        return $this->external_code;
    }

    public function addEntry(PODeliveryEntry $entry)
    {
        $this->entries->add($entry);
        $entry->setDelivery($this);
        return $this;
    }

    public function clearEntries()
    {
        $this->entries->clear();
        return $this;
    }

    public function getDateDeliver()
    {
        return $this->date_deliver;
    }

    public function getPurchaseOrder()
    {
        return $this->purchase_order;
    }

    public function getEntries()
    {
        return $this->entries;
    }
    
    public function generateCode()
    {
        $year = date('Y');
        $this->code = "DRS-".$year.'-'.str_pad($this->id,5, "0", STR_PAD_LEFT);
    }
    
    public function setReceived()
    {
        $this->status = self::STATUS_RECEIVED;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasCode($data);
        $this->dataTrackCreate($data);
        
        $data->external_code = $this->external_code;
        $data->date_deliver = $this->date_deliver;
        $data->po_id = $this->getPurchaseOrder()->getID();

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
