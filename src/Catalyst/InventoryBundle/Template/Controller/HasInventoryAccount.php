<?php

namespace Catalyst\InventoryBundle\Template\Controller;
use Catalyst\InventoryBundle\Entity\Account;

use Catalyst\InventoryBundle\Entity\Warehouse;
use Catalyst\PurchasingBundle\Entity\Supplier;

trait HasInventoryAccount
{
    protected function updateHasInventoryAccount($o, $data, $is_new)
    {
        
        if($is_new){
            
            $allow = false;
            $prefix = '';
            if($this->newBaseClass() instanceof Warehouse){
                $prefix = 'Warehouse: ';
                $allow = false;
            }else 
            if($this->newBaseClass() instanceof Supplier){
                $prefix = 'Supplier: ';
                $allow = true;
            }
                        
           
            $account = new Account();
            $account->setAllowNegative($allow);
            $account->setUserCreate($this->getUser());
            $account->setName($prefix.$o->getName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();
            $o->setInventoryAccount($account);
        }
    }
}
