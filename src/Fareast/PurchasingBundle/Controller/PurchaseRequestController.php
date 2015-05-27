<?php

namespace Fareast\PurchasingBundle\Controller;

use Catalyst\PurchasingBundle\Controller\PurchaseRequestController as BaseController;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\PurchasingBundle\Entity\PREntry;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class PurchaseRequestController extends BaseController
{
   public function __construct()
    {
        $this->repo = 'CatalystPurchasingBundle:PurchaseRequest';
        parent::__construct();
    }

    protected function padFormParams(&$params, $prod = null)
    {
        $inv = $this->get('catalyst_inventory');
        $params['prodgroup_opts'] = $inv->getProductGroupOptions(); 

        return $params;
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Request No', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateCreate', 'date_create', 'o', array($this, 'formatDate')),
            $grid->newColumn('Reference No', 'getReferenceCode', 'reference_num'),            
            $grid->newColumn('Date Needed', 'getDateNeeded', 'date_needed', 'o', array($this, 'formatDate')),
        );
    }

    public function getTypeAction($type_id)
    {
        $em = $this->getDoctrine()->getManager();

        $type = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $data = array();
        foreach($type as $t)
        {
            if($t->getTypeID() == $type_id)
            {
                $data[] = [
                    'name' => $t->getName(),
                    'id' => $t->getID(),
                ];
            }
        }

        return new JsonResponse($data);
    }

    public function getProductAction($prod_id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('CatalystInventoryBundle:Product')->findAll();

        $json = array();
        foreach($product as $p)
        {
            if($p->getID() == $prod_id)
            {
                $json[] = [
                    'uom' => $p->getUnitOfMeasure(),
                ];
            }
        }

        return new JsonResponse($json);
    }

    protected function update($o, $data, $is_new = false)
    {
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die();

        $em = $this->getDoctrine()->getManager();
        $o->setDateNeeded(new DateTime($data['date_need']));
        $o->setPurpose($data['purpose']);
        $o->setNotes($data['notes']);
        $o->setReferenceCode($data['reference_code']);
        // $o->setDepartment($data['department']);
        $this->updateTrackCreate($o,$data,$is_new);
        // clear entries
        $ents = $o->getEntries();
        foreach($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        // entries
        if (isset($data['en_prod_id']))
        {
            foreach ($data['en_prod_id'] as $index => $prod_id)
            {
                // fields
                $qty = $data['en_qty'][$index];
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new PREntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty);

                // add entry
                $o->addEntry($entry);
            }
        }
        // TODO: not sure if product entry is needed to have atleast 1, will add an else statement with validation exception for product entry.
    }    
}