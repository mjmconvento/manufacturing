<?php

namespace Catalyst\ContactBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;

/** 
* @ORM\Entity
* @ORM\Table(name="cnt_type")
*/
class ContactType
{
	use HasGeneratedID;

	/** @ORM\Column(type="string", length=50) */
	protected $type;

	public function __construct()
	{
		$this->initHasGeneratedID();
	}

	public function setContactType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function getContactType()
	{
		return $this->$type;
	}

	public function toData()
	{
		$data = new \stdClass();

		$this->dataHasGeneratedID();

		$data->type = $this->type;

		return $data;
	}
}