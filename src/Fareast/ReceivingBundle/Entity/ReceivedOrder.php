<?php

namespace Fareast\ReceivingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use DateTime;

/**
* @ORM\Entity
* @ORM\Table(name="rec_received_order")
*/
class ReceivedOrder
{
	use HasGeneratedID;
	use HasCode;
	use TrackCreate;

	const STATUS_DRAFT = 'Draft';
    const STATUS_PREPARED = 'Prepared';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CLOSED = 'Closed';

    /** @ORM\Column(type="date") */
    protected $date_deliver;


    /** @ORM\Column(type="string", length=20) */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\PurchasingBundle\Entity\PurchaseRequest")
     * @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     */
    protected $purchase_request;

    /**
     * @ORM\OneToMany(targetEntity="RODeliveryEntry", mappedBy="delivery", cascade={"persist"})
     */
    protected $entries;

    public function __construct()
    {
    	$this->initTrackCreate();
    	$this->date_deliver = new DateTime();
    	$this->entries = new ArrayCollection();
    	$this->status = self::STATUS_DRAFT;
    }

    public function setDateDeliver(DateTime $date)
    {
        $this->date_deliver = $date;
        return $this;
    }

    public function setPurchaseRequest(PurchaseRequest $ro)
    {
        $this->purchase_request = $ro;
        return $this;
    }

    public function addEntry(RODeliveryEntry $entry)
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

    public function getDateDeliverFormatted()
    {
        return $this->date_deliver->format('m/d/Y');
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getPurchaseRequest()
    {
        return $this->purchase_request;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getQuantity()
    {
        $total = 0;
        foreach ($this->entries as $e)
        {
            $total += $e->getQuantity();
        }
        return $total;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getPurchaseRequest()->getEntries() as $p)
        {
            foreach ($this->getEntries() as $e)
            {
                // if (($p->getRequestOrder()->getID() == $e->getDelivery()->getPurchaseOrder()->getID()) && ($p->getProduct()->getID() == $e->getProduct()->getRootProduct()->getID()))
                // {
                    $total += $e->getQuantity()*$p->getPrice();
                // }
            }
        }
        return number_format((float)$total, 2, '.', ''); 
    }

    public function getTotalDR()
    {
        return count($this);
    }

    public function getTotalItems()
    {
        return count($this->getEntries());
    }


    public function generateCode()
    {
        $year = date('Y');
        $this->code = "DR-".$year.'-'.str_pad($this->id,5, "0", STR_PAD_LEFT);
    }

    public function setStatus($status){
        $this->status = $status;
        return $this;
    }

    public function setDelivered()
    {
        $this->status = self::STATUS_DELIVERED;
    }

    public function setDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    public function setClosed()
    {
        $this->status = self::STATUS_CLOSED;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasCode($data);
        $this->dataTrackCreate($data);

        $data->date_deliver = $this->date_deliver;
        $data->pr_id = $this->getPurchaseRequest()->getID();

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}