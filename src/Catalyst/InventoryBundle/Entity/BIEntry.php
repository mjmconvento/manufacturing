<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;

use DateTime;
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
     * @ORM\ManyToOne(targetEntity="BorrowedTransaction", inversedBy="entries")
     * @ORM\JoinColumn(name="borrowed_id", referencedColumnName="id")
     */
    protected $borrowed;

    /** @ORM\Column(type="date", nullable=true) */
    protected $date_returned;

    public function __construct()
    {
        $this->initHasQuantity();        
    	$this->initHasGeneratedID();
    }

    public function setDateReturned(DateTime $date)
    {
        $this->date_returned = $date;
        return $this;
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

    public function getDateReturned()
    {
        return $this->date_returned;
    }

    public function getDateReturnedFormatted()
    {
        return $this->date_returned->format('m/d/Y');
    }

    public function toData()
    {
    	$data = new \stdClass();

    	$this->dataHasGeneratedID($data);
    	$this->dataHasProduct($data);
    	$this->dataHasQuantity($data);
        $data->borrowed_id = $this->getBorrowed()->getID();
        $data->date_returned = $this->date_returned->format('Y-m-d H:i:s');

		return $data;
    }

}