<?php

namespace Serenitea\OrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class RequestController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'ser_request';
        $this->title = 'Requested Item';

        $this->list_title = 'Requested Items';
        $this->list_type = 'dynamic';
    }
    
    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List', 'ser_request_index');

        $twig_file = 'SereniteaOrderBundle:Request:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();        

        $sample = $this->getUser();
        
        $params['sample'] = $sample;

        return $this->render($twig_file, $params);          
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('PO #', '', ''),
            $grid->newColumn('Date Issued', '', ''),
            $grid->newColumn('Target Delivery Date', '', ''),            
        );
    }
    
    protected function padFormParams(&$params, $object = null) {
        
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        
        $prods = $em->getRepository('CatalystInventoryBundle:Product')->findAll();
        $prod_opts = array();
        foreach ($prods as $prod)
            $prod_opts[$prod->getID()] = $prod->getName();
        $params['prod_opts'] = $prod_opts;

        $sample = $this->getUser()->getGroups();
        
        $params['sample'] = $sample;
        
        $params['status_opts'] = array(
            'draft' => 'Draft',
            'approved' => 'Approved',
            'fulfilled' => 'Fulfilled',
            'sent' => 'Sent',
            'cancelled' => 'Cancel',
        );
        
        return $params;
    }
    
   
    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }
    
    public function addEntryAction($entry_id)
    {
        $em = $this->getDoctrine()->getManager();

        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        $obj = $this->newBaseClass();

        $params = $this->getViewParams('Add');

        // $inv = $this->get('catalyst_inventory');
        $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($entry_id);

        $params['prod'] = array(
                'id' => $prod->getID(),
                'code' => $prod->getCode(),
                'name' => $prod->getName(),
//                'price' => $prod->getPriceSale()
            );

        $params['object'] = $obj;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.add');

        $this->padFormParams($params, $obj);

        return $this->render('SereniteaOrderBundle:Orders:add.html.twig', $params);
    }
    
    public function viewAction(){
        $this->title = 'Requested Items';
        $params = $this->getViewParams('', 'ser_request_view');

        return $this->render('SereniteaOrderBundle:Orders:view.html.twig', $params);
    }  
    
    public function formatDate($date)
    {
        return $date->format('m/d/Y');
    }
    
    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();

        // validate code
        // if (strlen($data['code']) > 0)
        //     $o->setCode($data['code']);
        // else
        //     throw new ValidationException('Cannot leave code blank');
        if ($is_new) {
            $o->setCode('');
        }

        $o->setDateIssue(new DateTime($data['date_issue']));

        // supplier
        $supp = $em->getRepository('CatalystPurchasingBundle:Supplier')->find($data['supplier_id']);
        if ($supp == null)
            throw new ValidationException('Could not find supplier.');
        
        $o->setSupplier($supp);

        // clear entries
        $ents = $o->getEntries();
        foreach ($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();

        // entries
        if (isset($data['en_prod_id']))
        {
            foreach ($data['en_prod_id'] as $index => $prod_id)
            {
                // fields
                $qty = $data['en_qty'][$index];
                $price = $data['en_price'][$index];
                $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);
                if ($prod == null)
                    throw new ValidationException('Could not find product.');

                // instantiate
                $entry = new POEntry();
                $entry->setProduct($prod)
                    ->setQuantity($qty)
                    ->setPrice($price);

                // add entry
                $o->addEntry($entry);
            }
        }
    }
}
