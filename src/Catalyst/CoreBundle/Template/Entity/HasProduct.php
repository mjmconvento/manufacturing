<?php

namespace Catalyst\CoreBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Product;

trait HasProduct
{
    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    public function setProduct(Product $prod)
    {
        $this->product = $prod;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }


    public function dataHasProduct($data)
    {
        $data->product = $this->product->toData();
    }
}
