<?php

namespace Catalyst\TemplateBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Catalyst\InventoryBundle\Entity\ProductGroup;
use Catalyst\InventoryBundle\Entity\Brand;
use Catalyst\InventoryBundle\Entity\ProductCodeReference;


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
        $code = ['PWD','SAN','SNK','SYR','TEA','DRY'];
        $x = 0;
        while ($x<=5){
            $ct = new ProductGroup();
            $ct->setName($group[$x]);
            $ct->setCode($code[$x]);
            $em->persist($ct);
            $x++;   
        }
        $em->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
}