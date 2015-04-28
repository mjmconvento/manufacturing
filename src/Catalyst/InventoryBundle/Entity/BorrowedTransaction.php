<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\UserBundle\Entity\Department;
use DateTime;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_borrowed_transaction")
 */
class BorrowedTransaction
{
	use HasGeneratedID;
	use Hascode;
	use TrackCreate;

	const STATUS_INCOMPLETE = 'Incomplete';
    const STATUS_COMPLETE = 'Complete';

    /** @ORM\Column(type="date") */
    protected $date_issue;

    /** 
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\Department")
     * @ORM\JoinColumn(name="dept_id", referencedColumnName="id")
     */
    protected $department;

    /** @ORM\Column(type="string", length=30, nullable=true) */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="BIEntry", mappedBy="borrowed", cascade={"persist"})
     */
    protected $entries;

    /** @ORM\Column(type="string", length=100, nullable=true) */
    protected $description;

    /** @ORM\Column(type="string", length=100, nullable=true) */
    protected $remarks;    

	public function __construct()
	{
		$this->initHasGeneratedID();
		$this->initTrackCreate();
		$this->entries = new ArrayCollection();
		$this->status = self::STATUS_INCOMPLETE;
	}	

    public function setDateReturned(DateTime $date)
    {
        $this->date_returned = $date;
        return $this;
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }

    public function setRemark($rmk)
    {
        $this->remarks = $rmk;
        return $this;
    }

    public function setDepartment(Department $dept)
    {
        $this->department = $dept;
        return $this;
    }

    public function setDateIssue(DateTime $date)
    {
        $this->date_issue = $date;
        return $this;
    }

    public function setStatus($status)
    {
        // TODO: check for invalid status
        $this->status = $status;
        return $this;
    }

    public function addEntry(BIEntry $entry)
    {
        $this->entries->add($entry);
        $entry->setBorrowed($this);
        return $this;
    }

    public function clearEntries()
    {
        $this->entries->clear();
        return $this;
    }
    
    public function getEntries()
    {
        return $this->entries;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getRemark()
    {
        return $this->remarks;
    }

    public function getTotalItem()
    {
        return count($this->getEntries());
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getDepartment()
    {
        return $this->department;
    }

    public function getDateReturned()
    {
        return $this->date_returned;
    }

    public function getDateIssueFormat()
    {
        return $this->date_issue->format('m/d/Y');
    }

    public function getDateReturnedFormatted()
    {
        return $this->date_returned->format('m/d/Y');
    }

    public function generateCode()
    {        
        $this->code = str_pad($this->id,5, "0", STR_PAD_LEFT);
    }

	public function toData()
	{
		$data = new \stdClass();
		$this->dataHasGeneratedID($data);
		$this->dataTrackCreate($data);
		$this->dataHasCode($data);
		$data->status = $this->status;
		$data->department = $this->department;
        $data->description = $this->description;
        $data->remarks = $this->remarks;
		$data->date_issue = $this->date_issue;
		$data->date_returned = $this->date_returned->format('Y-m-d H:i:s');

		$entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;		

		return $data;
	}
}