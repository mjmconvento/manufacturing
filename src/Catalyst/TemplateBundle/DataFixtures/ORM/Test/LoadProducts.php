<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\InventoryBundle\Entity\Product;


class LoadProducts implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $prodNames = ['Frost Powder', 'Sinker', 'Straw', 'Cup'];
        $groups = $em->getRepository('CatalystInventoryBundle:ProductGroup')->findAll();
        $brands = $em->getRepository('CatalystInventoryBundle:Brand')->findAll();
       
        foreach($prodNames as $name){
            $product = new Product();
            
            $brand = array_rand($brands, 1);
            $group = array_rand($groups, 1);
            $product->setName($name)
                    ->setSku('')
                    ->setStockMin(5)
                    ->setStockMax(10)
                    ->setUnitOfMeasure('pcs')
                    ->setBrand($brands[$brand])
                    ->setProductGroup($groups[$group]);
            
            
            $em->persist($product);
            $em->flush();
            $product->generateSku();
            $em->persist($product);
            $em->flush();
        }
        
        $em->flush();
    }
    
    public function getOrder()
    {
        return 2;
    }
}