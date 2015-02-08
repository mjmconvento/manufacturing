<?php

namespace Catalyst\InventoryBundle\Template\Entity;

use Doctrine\ORM\Mapping as ORM;
use Catalyst\InventoryBundle\Entity\Account;

trait HasAccount
{
    /**
     * @ORM\ManyToOne(targetEntity="\Catalyst\InventoryBundle\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;

    protected function initHasAccount()
    {
        $this->account = null;
    }

    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function dataHasAccount($data)
    {
        if ($this->account == null)
            $data->account = null;
        else
            $data->account = $this->account->toData();
    }
}
