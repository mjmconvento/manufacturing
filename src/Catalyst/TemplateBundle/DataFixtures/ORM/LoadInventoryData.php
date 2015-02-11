<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\InventoryBundle\Entity\ProductGroup;
use Catalyst\InventoryBundle\Entity\Brand;


class LoadInventoryData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        $brands = ['Sola', 'Nestea', 'Lipton',];
        foreach($brands as $type){
            $pt = new Brand();
            $pt->setName($type);
            $em->persist($pt);
        }
        $em->flush();
    
    
        $group = ['Powder', 'Sanitary', 'Sinker', 'Syrup', 'Teabags', 'Dry Goods'];
        foreach($group as $type){
            $ct = new ProductGroup();
            $ct->setName($type);
            $em->persist($ct);
        }
        $em->flush();    
    }
    
    public function getOrder()
    {
        return 1;
    }
}