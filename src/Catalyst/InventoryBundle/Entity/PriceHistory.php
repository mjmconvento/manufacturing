<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;

use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_price_history")
 */
class PriceHistory
{
    use HasGeneratedID;
    use HasProduct;
    use TrackCreate;
    
    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $price_sale;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $price_purchase;

    

    public function __construct()
    {
        $this->price_sale = 0.00;
        $this->price_purchase = 0.00;

        $this->initTrackCreate();
    }

    public function setPriceSale($price)
    {
        $this->price_sale = $price;
        return $this;
    }

    public function setPricePurchase($price)
    {
        $this->price_purchase = $price;
        return $this;
    }

    public function getPriceSale()
    {
        return $this->price_sale;
    }

    public function getPricePurchase()
    {
        return $this->price_purchase;
    }


    public function toData()
    {
        $data = new stdClass();

        $this->dataHasGeneratedID($data);
        $this->dataHasProduct($data);
        $this->dataTrackCreate($data);
        $data->price_sale = $this->price_sale;
        $data->price_purchase = $this->price_purchase;


        return $data;
    }
}
