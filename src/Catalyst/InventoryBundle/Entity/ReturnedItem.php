<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\InventoryBundle\Entity\BIEntry;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_returned_item")
 */
class ReturnedItem
{
    use HasGeneratedID;   
    use HasQuantity;

    /**
     * @ORM\ManyToOne(targetEntity="BIEntry", inversedBy="returned", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="entry_id", referencedColumnName="id")
     */
    protected $bientry;

    /** @ORM\Column(type="date", nullable=true) */
    protected $date_returned;


    public function __construct()
    {
        $this->initHasQuantity();       
        $this->initHasGeneratedID();
    }   

    public function getDateReturned()
    {
        return $this->date_returned;
    }

    public function setBIEntry(BIEntry $entry)
    {
        $this->bientry = $entry;
        $this->entry_id = $entry->getID();
        return $this;
    }

    public function setDateReturned(DateTime $date)
    {
        $this->date_returned = $date;
        return $this;
    }

}
