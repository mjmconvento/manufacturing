<?php

namespace Catalyst\CoreBundle\Template\Entity;
use Catalyst\ValidationException;
use Doctrine\ORM\Mapping as ORM;

trait HasQuantity
{
    /** @ORM\Column(type="decimal", precision=10, scale=2) */
    protected $quantity;


    public function setQuantity($quantity)
    {
        if(is_numeric($quantity)){
            $this->quantity = $quantity;
        }else {
            throw new ValidationException('Invalid value for quantity');
        }
        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
    
    public function initHasQuantity()
    {
        $this->quantity = 0.00;
    }


    public function dataHasQuantity($data)
    {
        $data->quantity = $this->quantity;
    }
}
