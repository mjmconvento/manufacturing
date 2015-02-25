<?php

namespace Catalyst\InventoryBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Warehouse;

trait HasWarehouse
{
    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    protected $warehouse;

    public function setWarehouse(Warehouse $prod)
    {
        $this->warehouse = $prod;
        return $this;
    }

    public function getWarehouse()
    {
        return $this->warehouse;
    }


    public function dataHasWarehouse($data)
    {
        $data->warehouse = $this->warehouse->toData();
    }
}
