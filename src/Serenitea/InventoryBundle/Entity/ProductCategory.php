<?php

namespace Serenitea\InventoryBundle\Entity;

use Catalyst\InventoryBundle\Entity\ProductGroup;
use Doctrine\ORM\Mapping as ORM;
use stdClass;

/**
* @ORM\Entity
* @ORM\Table(name="inv_product_category")
*/
class ProductCategory extends ProductGroup
{
	/** @ORM\Column(type="string", length=20) */
	protected $code;

	public function __construct()
	{
		
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
		$data->id = $this->id;
        $data->name = $this->name;
		$data->code = $this->code;

		return $data;
	}
}