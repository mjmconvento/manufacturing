<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasName;
use Catalyst\PurchasingBundle\Entity\Supplier;

use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_product")
 */
class Product
{
    use HasGeneratedID;
    use HasName;
    
    /** @ORM\Column(type="string", length=25, nullable=false) */
    protected $sku;

    /** @ORM\Column(type="string", length=20) */
    protected $uom;

    /** @ORM\Column(type="boolean") */
    protected $flag_service;

    /** @ORM\Column(type="boolean") */
    protected $flag_sale;

    /** @ORM\Column(type="boolean") */
    protected $flag_purchase;

    /** @ORM\Column(type="decimal", precision=13, scale=2) */
    protected $price_sale;

    /** @ORM\Column(type="decimal", precision=13, scale=2) */
    protected $price_purchase;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $stock_min;

    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $stock_max;

    /** @ORM\Column(type="string", length=50) */
    protected $supp_code;

    /**
    * @ORM\ManyToOne(targetEntity="\Catalyst\PurchasingBundle\Entity\Supplier")
    * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
    */
    protected $supplier;

    /**
     * @ORM\ManyToOne(targetEntity="ProductGroup")
     * @ORM\JoinColumn(name="prodgroup_id", referencedColumnName="id")
     */
    protected $prodgroup;

    /**
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="variants")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /** @ORM\OneToMany(targetEntity="Product", mappedBy="parent") */
    protected $variants;

    /** @ORM\OneToMany(targetEntity="ProductAttribute", mappedBy="product") */
    protected $attributes;

    /** @ORM\Column(type="json_array") */
    protected $attribute_hash;


    public function __construct()
    {
        $this->price_sale = 0.00;
        $this->price_purchase = 0.00;
        $this->tasks = new ArrayCollection();

        $this->stock_min = 0.00;
        $this->stock_max = 0.00;

        $this->flag_service = false;
        $this->flag_sale = false;
        $this->flag_purchase = false;

        $this->parent = null;
        $this->variants = new ArrayCollection();

        $this->attributes = new ArrayCollection();

        $this->attribute_hash = array();
    }

    public function setSKU($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    public function setUnitOfMeasure($uom)
    {
        $this->uom = $uom;
        return $this;
    }

    public function setFLagService($flag = true)
    {
        $this->flag_service = $flag;
        return $this;
    }

    public function setFlagSale($flag = true)
    {
        $this->flag_sale = $flag;
        return $this;
    }

    public function setFlagPurchase($flag = true)
    {
        $this->flag_purchase = $flag;
        return $this;
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

    public function setProductGroup(ProductGroup $group)
    {
        $this->prodgroup = $group;
        return $this;
    }

    public function setBrand(Brand $brand)
    {
        $this->brand = $brand;
        return $this;
    }

    public function setStockMin($stock)
    {
        $this->stock_min = $stock;
        return $this;
    }

    public function setStockMax($stock)
    {
        $this->stock_max = $stock;
        return $this;
    }

    public function setSupplier(Supplier $supp)
    {
        $this->supplier = $supp;
        $this->supplier_id = $supp->getID();
        return $this;
    }

    public function setSupplierCode($supp_code)
    {
        $this->supp_code = $supp_code;
        return $this;
    }

    public function getSupplier()
    {
        return $this->supplier;
    }

    public function getSupplierCode()
    {
        return $this->supp_code;
    }

    public function getSKU()
    {
        return $this->sku;
    }

    public function getUnitOfMeasure()
    {
        return $this->uom;
    }

    public function isService()
    {
        return $this->flag_service;
    }

    public function canSell()
    {
        return $this->flag_sale;
    }

    public function canPurchase()
    {
        return $this->flag_purchase;
    }

    public function getPriceSale()
    {
        return $this->price_sale;
    }

    public function getPricePurchase()
    {
        return $this->price_purchase;
    }

    public function getProductGroup()
    {
        return $this->prodgroup;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function getStockMin()
    {
        return $this->stock_min;
    }

    public function getStockMax()
    {
        return $this->stock_max;
    }

    public function setParent(Product $prod)
    {
        $this->parent = $prod;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addVariant(Product $prod)
    {
        $prod->setParent($this);
        $this->variants->add($prod);
        return $this;
    }

    public function getVariants()
    {
        return $this->variants;
    }

    public function addVariantAttribute($attr)
    {
        $this->attribute_hash[$attr] = $attr;
        return $this;
    }

    public function getVariantAttributes()
    {
        return $this->attribute_hash;
    }

    public function toData()
    {
        $data = new stdClass();
        $data->id = $this->id;
        $data->sku = $this->sku;
        $data->name = $this->name;
        $data->supplier_id = $this->supplier->getID();
        $data->prodgroup_id = $this->prodgroup->getID();
        $data->uom = $this->uom;
        $data->supp_code = $this->supp_code;
        $data->flag_service = $this->flag_service;
        $data->flag_sale = $this->flag_sale;
        $data->flag_purchase = $this->flag_purchase;
        $data->price_sale = $this->price_sale;
        $data->price_purchase = $this->price_purchase;

        // brand
        if ($this->brand == null)
            $data->brand_id = null;
        else
            $data->brand_id = $this->brand->getID();

        return $data;
    }
}
