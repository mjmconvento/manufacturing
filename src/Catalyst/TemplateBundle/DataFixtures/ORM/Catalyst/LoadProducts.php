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


        $mollases = new Product();
        $mollases->setName(DailyConsumption::PROD_MOLLASES)
            ->setSKU('mollases')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('gallons');
        $em->persist($mollases);


        $bunker = new Product();
        $bunker->setName(DailyConsumption::PROD_BUNKER)
            ->setSKU('bunker')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('liters');
        $em->persist($bunker);

        $sulfuric_acid = new Product();
        $sulfuric_acid->setName(DailyConsumption::PROD_SULFURIC_ACID)
            ->setSKU('sulfuric_acid')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('gallons');
        $em->persist($sulfuric_acid);

        $caustic_soda = new Product();
        $caustic_soda->setName(DailyConsumption::PROD_CAUSTIC_SODA)
            ->setSKU('caustic_soda')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('liters');
        $em->persist($caustic_soda);

        $urea = new Product();
        $urea->setName(DailyConsumption::PROD_UREA)
            ->setSKU('urea')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('bags');
        $em->persist($urea);

        $salt = new Product();
        $salt->setName(DailyConsumption::PROD_SALT)
            ->setSKU('urea')
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure('bags');
        $em->persist($salt);


        $em->flush();
    
    }
    
            
    public function getOrder()
    {
        return 3;
    }
}