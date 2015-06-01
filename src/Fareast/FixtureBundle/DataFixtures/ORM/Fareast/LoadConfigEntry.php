<?php

namespace Fareast\FixtureBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\ConfigurationBundle\Entity\ConfigEntry;
use DateTime;

class LoadConfigEntry  extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $main_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->findOneByName('Main Warehouse')->getID();
        $adj_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->findOneByName('Adjustment Warehouse')->getID();
        $prod_warehouse = $em->getRepository('CatalystInventoryBundle:Warehouse')->findOneByName('Production Tank')->getID();

        $config_entry_main = new ConfigEntry('catalyst_warehouse_main', $main_warehouse);
        $em->persist($config_entry_main);

        $config_entry_adj = new ConfigEntry('catalyst_warehouse_stock_adjustment', $adj_warehouse);
        $em->persist($config_entry_adj);

        $config_entry_prod = new ConfigEntry('catalyst_warehouse_production_tank', $prod_warehouse);
        $em->persist($config_entry_prod);   

        $em->flush();
    }
    
    public function getOrder()
    {
        return 6;
    }
}