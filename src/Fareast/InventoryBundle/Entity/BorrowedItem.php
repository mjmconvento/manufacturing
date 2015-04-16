<?php

namespace Fareast\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\CoreBundle\Template\Entity\HasGeneratedID;
use Catalyst\CoreBundle\Template\Entity\TrackCreate;
use Catalyst\CoreBundle\Template\Entity\HasCode;
use Catalyst\UserBundle\Entity\User;
use DateTime;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_borrowed_item")
 */
class BorrowedItem
{
	use HasGeneratedID;
	use Hascode;
	use TrackCreate;
	
	/** @ORM\Column(type="date") */
    protected $date_returned;

    /** @ORM\Column(type="date") */
    protected $date_issue;

    /** 
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_issuedto_id", referencedColumnName="id")
     */
    protected $issued_to;

    /**
     * @ORM\OneToMany(targetEntity="BorrowedEntry", mappedBy="borrowed", cascade={"persist"})
     */
    protected $entries;

	public function __construct()
	{
		$this->initHasGeneratedID();
		$this->entries = new ArrayCollection();
	}	

    public function setDateReturned(DateTime $date)
    {
        $this->date_returned = $date;
        return $this;
    }

    public function setIssuedTo(User $user)
    {
        $this->issued_to = $user;
        return $this;
    }

    public function setDateIssue(DateTime $date)
    {
        $this->date_issue = $date;
        return $this;
    }

    public function addEntry(BorrowedEntry $entry)
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

    public function getDateIssue()
    {
        return $this->date_issue;
    }

    public function getIssuedTo()
    {
        return $this->issued_to;
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

	public function toData()
	{
		$data = new \stdClass();
		$this->dataHasGeneratedID($data);
		$this->dataTrackCreate($data);
		$this->dataHasCode($data);
		$data->issued_to = $this->issued_to;
		$data->date_issue = $this->date_issue;
		$data->date_returned = $this->date_returned->format('Y-m-d H:i:s');

		$entries = array();
        foreach ($this->entries as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;		

		return $data;
	}
}