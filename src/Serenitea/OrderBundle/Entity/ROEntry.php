<?php

namespace Serenitea\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Serenitea\OrderBundle\Entity\RODelivery;

use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="req_order_entry")
 */
class ROEntry
{
	use HasGeneratedID;
    use HasQuantity;
    use HasProduct;

    /** @ORM\Column(type="integer") */
    protected $req_id;

    /**
     * @ORM\ManyToOne(targetEntity="RequestOrder")
     * @ORM\JoinColumn(name="req_id", referencedColumnName="id")
     */
    protected $request_order;

    public function __construct()
    {
        $this->initHasQuantity();
    }

    public function setRequestOrder(RequestOrder $request)
    {
        $this->request_order = $request;
        $this->req_id = $request->getID();
        return $this;
    }

    public function getRequestOrder()
    {
        return $this->request_order;
    }

     public function getDeliveredQuantity()
    {
        $deliveries = $this->getRequestOrder()->getDeliveries();
        $qty = 0;
        foreach($deliveries as $delivery){
            
            if($delivery->getStatus() == RODelivery::STATUS_RECEIVED){
                foreach($delivery->getEntries() as $delivery_entry){
                    if($delivery_entry->getProduct()->getRootProduct() === $this->getProduct()){
                        $qty += $delivery_entry->getQuantity();
                    } 
                }
            }
        }
        return $qty;
    }

    public function toData()
    {
        $data = new \stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasQuantity($data);
        $this->dataHasProduct($data);        
        $data->req_id = $this->getRequestOrder()->getID();

        return $data;
    }

}