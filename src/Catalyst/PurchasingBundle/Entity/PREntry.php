<?php

namespace Catalyst\PurchasingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasProduct;

use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="pur_request_entry")
 */
class PREntry
{
    use HasGeneratedID;
    use TrackCreate;
    use HasQuantity;
    use HasProduct;

    /**
     * @ORM\ManyToOne(targetEntity="PurchaseRequest")
     * @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     */
    protected $purchase_request;

    public function __construct()
    {
        $this->initHasQuantity();
        $this->initTrackCreate();
    }

    public function setPurchaseRequest(PurchaseRequest $pr)
    {
        $this->purchase_request = $pr;
        return $this;
    }


    public function getPurchaseRequest()
    {
        return $this->purchase_request;
    }


    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataTrackCreate($data);
        $this->dataHasQuantity($data);
        $this->dataHasProduct($data);
        $data->pr_id = $this->getPurchaseRequest()->getID();

        return $data;
    }
}

