<?php

namespace Fareast\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\InventoryBundle\Template\Entity\HasProduct;
use Catalyst\CoreBundle\Template\Entity\HasQuantity;


/**
 * @ORM\Entity
 * @ORM\Table(name="inv_issued_entry")
 */
class IssuedEntry
{
	use HasGeneratedID;
	use HasProduct;
	use HasQuantity;

	/**
     * @ORM\ManyToOne(targetEntity="IssuedItem")
     * @ORM\JoinColumn(name="issued_id", referencedColumnName="id")
     */
    protected $issued;

    /** @ORM\Column(type="string", length=80, nullable=true) */
    protected $description;

    /** @ORM\Column(type="string", length=80, nullable=true) */
    protected $remarks;

    public function __construct()
    {
    	$this->initHasGeneratedID();
    }

    public function setIssued(IssuedItem $issued)
    {
    	$this->issued = $issued;
    	return $this;
    }

    public function setRemarks($remarks)
    {
    	$this->remarks = $remarks;
    	return $this;
    }

    public function setDescription($desc)
    {
    	$this->description = $desc;
    	return $this;
    }

    public function getIssued()
    {
    	return $this->issued;
    }

    public function getRemarks()
    {
    	return $this->remarks;
    }

    public function getDescription()
    {
    	return $this->description;
    }

    public function toData()
    {
    	$data = new \stdClass();

    	$this->dataHasGeneratedID($data);
    	$this->dataHasProduct($data);
    	$this->dataHasQuantity($data);
    	$data->description = $this->description;
		$data->remarks = $this->remarks;

		return $data;
    }

}