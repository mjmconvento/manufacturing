<?php

namespace Serenitea\InventoryBundle\Entity;

use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\PurchasingBundle\Entity\Supplier;
use Doctrine\ORM\Mapping as ORM;
use stdClass;

/**
* @ORM\Entity
* @ORM\Table(name="inv_supply")
*/
class Supply extends Product
{
	/**
	* @ORM\ManyToOne(targetEntity="\Catalyst\PurchasingBundle\Entity\Supplier")
	* @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
	*/
	protected $supplier;

	/**
	* @ORM\ManyToOne(targetEntity="ProductCategory")
	* JoinColumn(name="prod_cat_id", referencedColumnName="id")
	*/
	protected $prodcat;

	/** @ORM\Column(type="string", length=50) */
	protected $supp_code;

	public function __construct()
	{

	}

	public function setSupplier(Supplier $supp)
	{
		$this->supplier = $supp;
		$this->supplier_id = $supp->getID();
		return $this;
	}

	public function getSupplier()
	{
		return $this->supplier;
	}

	public function setProductCategory(ProductCategory $prodcat)
	{
		$this->prodcat = $prodcat;
		$this->prod_cat_id = $prodcat->getID();
		return $this;
	}

	public function getProductCategory()
	{
		return $this->prodcat;
	}

	public function setSupplierCode($supp_code)
	{
		$this->supp_code = $supp_code;
		return $this;
	}

	public function getSupplierCode()
	{
		return $this->supp_code;
	}

	public function toData()
	{
		$data = new stdClass();

        $data->id = $this->id;
        $data->sku = $this->sku;
        $data->name = $this->name;
        $data->prod_cat_id = $this->prodcat->getID();
        $data->supplier_id = $this->supplier->getID();
        $data->uom = $this->uom;
        $data->supp_code = $this->supp_code;
        $data->price_sale = $this->price_sale;
        $data->price_purchase = $this->price_purchase;

        return $data;
	}
}