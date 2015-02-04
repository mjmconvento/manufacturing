<?php

namespace Catalyst\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Catalyst\UserBundle\Entity\User;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="inv_transaction")
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_in;

    /**
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="transaction", cascade={"persist"})
     */
    protected $entries;

    public function __construct()
    {
        $this->date_in = new DateTime();
        $this->entries = new ArrayCollection();
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
        return $this;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function addEntry(Entry $entry)
    {
        $entry->setTransaction($this);
        $this->entries->add($entry);
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getDateCreate()
    {
        return $this->date_in;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function checkBalance()
    {
        // check entries if all credits = debits per product
        $index_check = array();

        // go through entries
        foreach ($this->entries as $entry)
        {
            $id = $entry->getProduct()->getID();

            // build the index checker
            if (!isset($index_check[$id]))
                $index_check[$id] = array('debit' => '0.00', 'credit' => '0.00');

            $index_check[$id]['debit'] = bcadd($index_check[$id]['debit'], $entry->getDebit(), 2);
            $index_check[$id]['credit'] = bcadd($index_check[$id]['credit'], $entry->getCredit(), 2);
        }

        foreach ($index_check as $ic)
        {
            if ($ic['debit'] != $ic['credit'])
                return false;
        }

        return true;
    }

    public function toData()
    {
        $entries = array();

        $data = new \stdClass();
        $data->id = $this->id;
        $data->date_in = $this->date_in;
        $data->description = $this->description;
        $data->user = $this->getUser()->getID();

        foreach ($this->getEntries() as $entry)
            $entries[] = $entry->toData();
        $data->entries = $entries;

        return $data;
    }
}
