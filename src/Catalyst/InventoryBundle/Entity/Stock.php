<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Catalyst\CoreBundle\Template\Entity\HasProduct;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_stock")
 */
class Stock
{
    use HasGeneratedID;
    use HasQuantity;
    use HasProduct;

    /**
     * @ORM\ManyToOne(targetEntity="Warehouse")
     * #ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    public function setWarehouse(Warehouse $wh)
    {
        $this->warehouse = $wh;
        return $this;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }

}
