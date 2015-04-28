<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasName;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_product_group")
 */
class ProductGroup
{
    use HasGeneratedID;
    use HasName;

    /** @ORM\Column(type="string", length=50, nullable = true) */
    protected $code;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="prodgroup")
     */
    protected $products;

    public function __construct()
    {
        $this->initHasGeneratedID();
        $this->initHasName();

        $this->products = new ArrayCollection();
    }

    public function addProduct(Product $product)
    {
        $this->products->add($product);
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function toData()
    {
        $data = new stdClass();
        $data->code = $this->code;

        $this->dataHasGeneratedID();
        $this->dataHasName();

        return $data;
    }
}

