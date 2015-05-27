<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fareast\ManufacturingBundle\Entity\DailyConsumption;
use Catalyst\InventoryBundle\Entity\Product;

class LoadProducts extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $mollases = DailyConsumption::PROD_MOLLASES;
        $bunker = DailyConsumption::PROD_BUNKER;
        $sulfur = DailyConsumption::PROD_SULFURIC_ACID;
        $caustic = DailyConsumption::PROD_CAUSTIC_SODA;
        $urea = DailyConsumption::PROD_UREA;
        $salt = DailyConsumption::PROD_SALT;

        $products = [$mollases, $bunker, $sulfur, $caustic, $urea, $salt];
        foreach($products as $p){
            $p = new Product();
            $p->setName($p)
                ->setTypeID(Product::TYPE_RAW_MATERIAL);
            $em->persist($p);
        }
        $em->flush();
    
    }
    
            
    public function getOrder()
    {
        return 3;
    }
}