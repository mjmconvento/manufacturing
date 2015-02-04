<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Catalyst\CoreBundle\Model\HasGeneratedID;
use Catalyst\CoreBundle\Model\TrackCreate;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_account")
 */
class Account
{
    use HasGeneratedID;
    use TrackCreate;
    
    /** @ORM\Column(type="string", length=80) */
    protected $name;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
