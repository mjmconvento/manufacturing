<?php

namespace Fareast\FixtureBundle\DataFixtures\ORM\Catalyst;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Fareast\ManufacturingBundle\Entity\DailyConsumption;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ProductGroup;
use Catalyst\InventoryBundle\Entity\Brand;
use Fareast\ManufacturingBundle\Entity\ShiftReport;

class LoadProduct extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        // find user Administrator
        $user = $em->getRepository('CatalystUserBundle:User')->findOneByName('Administrator');
        
        // Add product Group
        $product_group_1 = new ProductGroup();
        $product_group_1->setName('Product Group 1');
        $em->persist($product_group_1);

        $product_group_2 = new ProductGroup();
        $product_group_2->setName('Product Group 2');
        $em->persist($product_group_2);

        // Add brand
        $product_brand_1 = new Brand();
        $product_brand_1->setName('Product Brand 1');
        $em->persist($product_brand_1);

        $product_brand_2 = new Brand();
        $product_brand_2->setName('Product Brand 2');
        $em->persist($product_brand_2);


        // Add products for daily consumptions
        $mollases = $this->createProduct(DailyConsumption::PROD_MOLLASES, 'mollases' , 'gallons', $product_group_1, $product_brand_1, $user);
        $em->persist($mollases);

        $bunker = $this->createProduct(DailyConsumption::PROD_BUNKER, 'bunker' , 'liters', $product_group_1, $product_brand_1, $user);
        $em->persist($bunker);

        $sulfuric_acid = $this->createProduct(DailyConsumption::PROD_SULFURIC_ACID, 'sulfuric_acid' , 'gallons', $product_group_1, $product_brand_1, $user);
        $em->persist($sulfuric_acid);

        $caustic_soda = $this->createProduct(DailyConsumption::PROD_CAUSTIC_SODA, 'caustic_soda' , 'liters', $product_group_1, $product_brand_1, $user);
        $em->persist($caustic_soda);

        $urea = $this->createProduct(DailyConsumption::PROD_UREA, 'urea' , 'bags', $product_group_1, $product_brand_1, $user);
        $em->persist($urea);

        $salt = $this->createProduct(DailyConsumption::PROD_SALT, 'salt' , 'bags', $product_group_1, $product_brand_1, $user);
        $em->persist($salt);


        // Add products for shift reports
        $heads_alcohol = new Product();
        $heads_alcohol->setName(ShiftReport::PROD_HEADS_ALCOHOL)
            ->setSKU('heads-alcohol')
            ->setTypeID(Product::TYPE_FINISHED_GOOD)
            ->setUnitOfMeasure('liters')
            ->setProductGroup($product_group_1)
            ->setBrand($product_brand_1)
            ->setUserCreate($user)
            ->setUserUpdate($user);
        $em->persist($heads_alcohol);

        $fine_alcohol = new Product();
        $fine_alcohol->setName(ShiftReport::PROD_FINE_ALCOHOL)
            ->setSKU('fine-alcohol')
            ->setTypeID(Product::TYPE_FINISHED_GOOD)
            ->setUnitOfMeasure('liters')
            ->setProductGroup($product_group_1)
            ->setBrand($product_brand_1)
            ->setUserCreate($user)
            ->setUserUpdate($user);
        $em->persist($fine_alcohol);

        $em->flush();
    }
    
    protected function createProduct($name, $sku, $uom, $product_group, $product_brand, $user)
    {
        $product = new Product();
        $product->setName($name)
            ->setSKU($sku)
            ->setTypeID(Product::TYPE_RAW_MATERIAL)
            ->setUnitOfMeasure($uom)
            ->setProductGroup($product_group)
            ->setBrand($product_brand)
            ->setUserCreate($user)
            ->setUserUpdate($user);

        return $product;
    }

    public function getOrder()
    {
        return 2;
    }
}