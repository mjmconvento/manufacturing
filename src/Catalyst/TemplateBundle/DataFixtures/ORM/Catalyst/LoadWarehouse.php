<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\InventoryBundle\Entity\Warehouse;
use DateTime;

class LoadWarehouse  extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $user = $em->getRepository('CatalystUserBundle:User')->findOneByName('Administrator');

        $inv_acc_main = $em->getRepository('CatalystInventoryBundle:Account')->findOneByName('Warehouse: Main Warehouse');
        $inv_acc_adj = $em->getRepository('CatalystInventoryBundle:Account')->findOneByName('Warehouse: Adjustment Warehouse');
        $inv_acc_prod = $em->getRepository('CatalystInventoryBundle:Account')->findOneByName('Warehouse: Production Tank');
        
        $main_warehouse = new Warehouse();
        $main_warehouse->setName('Main Warehouse')
            ->setDateCreate(new DateTime())
            ->setUserCreate($user)
            ->setInventoryAccount($inv_acc_main);
        $em->persist($main_warehouse);

        $adj_warehouse = new Warehouse();
        $adj_warehouse->setName('Adjustment Warehouse')
            ->setDateCreate(new DateTime())
            ->setUserCreate($user)
            ->setInventoryAccount($inv_acc_adj);
        $em->persist($adj_warehouse);

        $prod_warehouse = new Warehouse();
        $prod_warehouse->setName('Production Tank')
            ->setDateCreate(new DateTime())
            ->setUserCreate($user)
            ->setInventoryAccount($inv_acc_prod);
        $em->persist($prod_warehouse);

        $em->flush();
    }
    
    public function getOrder()
    {
        return 5;
    }
}