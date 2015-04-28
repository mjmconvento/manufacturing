<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;


/**
 * @ORM\Entity
 * @ORM\Table(name="inv_bi_entry")
 */
class BIEntry
{
	use HasGeneratedID;
	use HasProduct;
	use HasQuantity;

	/**
     * @ORM\ManyToOne(targetEntity="BorrowedTransaction")
     * @ORM\JoinColumn(name="borrowed_id", referencedColumnName="id")
     */
    protected $borrowed;

    /** @ORM\Column(type="date") */
    protected $date_returned;

    public function __construct()
    {
        $this->initHasQuantity();        
    	$this->initHasGeneratedID();
    }

    public function setBorrowed(BorrowedTransaction $borrowed)
    {
    	$this->borrowed = $borrowed;
        $this->borrowed_id = $borrowed->getID();
    	return $this;
    }
    
    public function getBorrowed()
    {
    	return $this->borrowed;
    }

    public function toData()
    {
    	$data = new \stdClass();

    	$this->dataHasGeneratedID($data);
    	$this->dataHasProduct($data);
    	$this->dataHasQuantity($data);
    	$data->description = $this->description;
		$data->remarks = $this->remarks;
        $data->borrowed_id = $this->getBorrowed()->getID();

		return $data;
    }

}