<?php

namespace Serenitea\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="req_delivery")
 */
class RODelivery
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
     * @ORM\ManyToOne(targetEntity="RequestOrder")
     * @ORM\JoinColumn(name="req_id", referencedColumnName="id")
     */
    protected $request_order;

    /**
     * @ORM\OneToMany(targetEntity="RODeliveryEntry", mappedBy="delivery", cascade={"persist"})
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

    public function setRequestOrder(RequestOrder $ro)
    {
        $this->request_order = $ro;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function getRequestOrder()
    {
        return $this->request_order;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function generateCode()
    {
        $year = date('Y');
        $this->code = "DR-".$year.'-'.str_pad($this->id,5, "0", STR_PAD_LEFT);
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
        $data->req_id = $this->getRequestOrder()->getID();

        $entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}