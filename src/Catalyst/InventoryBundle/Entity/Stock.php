<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\InventoryBundle\Template\Entity\HasInventoryAccount;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_stock")
 */
class Stock
{
    use HasGeneratedID;
    use HasQuantity;
    use HasProduct;
    use HasInventoryAccount;

    public function __construct()
    {
    	$this->initHasInventoryAccount();
    }    
}
