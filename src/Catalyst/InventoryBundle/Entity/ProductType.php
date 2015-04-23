<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasName;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_product_type")
 */
class ProductType
{
	use HasGeneratedID;
	use HasName;

	public function __construct()
	{
		$this->initHasGeneratedID();
	}

	public function toData()
	{
		$data = new \stdClass();
		$this->dataHasGeneratedID($data);
        $this->dataHasName($data);
        return $data;
	}
}