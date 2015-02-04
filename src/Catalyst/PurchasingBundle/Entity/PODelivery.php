<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_po_delivery")
 */
class PODelivery
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $code;

    /** @ORM\Column(type="datetime") */
    protected $date_in;

    /** @ORM\Column(type="date") */
    protected $date_deliver;

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
        $this->date_in = new DateTime();
        $this->date_delivery = new DateTime();
        $this->entries = new ArrayCollection();
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
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

    public function getID()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getDateCreate()
    {
        return $this->date_in;
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

    public function toData()
    {
        $data = new \stdClass();

        $data->id = $this->id;
        $data->code = $this->code;
        $data->date_in = $this->date_in;
        $data->date_deliver = $this->date_deliver;
        $data->po_id = $this->getPurchaseOrder()->getID();

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
