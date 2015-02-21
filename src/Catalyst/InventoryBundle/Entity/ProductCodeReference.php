<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;

/**
* @ORM\Entity
* @ORM\Table(name="inv_product_code_ref")
*/
class ProductCodeReference
{
	use HasGeneratedID;	

	/**
	* @ORM\ManyToOne(targetEntity="ProductGroup")
	* @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	*/
	protected $category;

	/**
	* @ORM\Column(type="integer")	
	*/
	protected $last_inserted;

	public function __construct()
	{		
	}

	public function setCategory(ProductGroup $cat)
	{
		$this->category = $cat;
		$this->category_id = $cat->getID();
		return $this;
	}

	public function setLastInserted($last)
	{
		$this->last_inserted = $last;
		return $this;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function getLastInserted()
	{
		return $this->last_inserted;
	}

	public function toData()
	{
		$data = new stdClass();

		$data->category_id = $this->getCategory()->getID();
		$data->last_inserted = $this->last_inserted;

		return $data;
	}
}