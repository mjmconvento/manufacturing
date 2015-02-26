<?php

namespace Serenitea\OnlineFormsBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Serenitea\OnlineFormsBundle\Entity\Pullout;
use Serenitea\OnlineFormsBundle\Entity\PulloutEntry;
use DateTime;

use Catalyst\CoreBundle\Template\Controller\TrackCreate;

class PulloutController extends CrudController
{
    use TrackCreate;
    public function __construct()
    {
        $this->route_prefix = 'ser_pullout';
        $this->title = 'Pull Out Form';

        $this->list_title = 'Pull Out Forms';
        $this->list_type = 'dynamic';
        
    }
    
    protected function update($o, $data, $is_new = false)
    {
        $this->updateTrackCreate($o, $data, $is_new);
        $em = $this->getDoctrine()->getManager();

        if($is_new){
            $o->setWarehouse($this->getUser()->getWarehouse());
            $o->setCode('');
        }
        
        $o->setTo($data['to']);
        $o->setCc($data['cc']);
        $o->setStatus($data['status']);
        
        $ents = $o->getEntries();
        foreach ($ents as $ent)
            $em->remove($ent);
        $o->clearEntries();
        
        if (isset($data['en_report_date']))
        {
        foreach ($data['en_report_date'] as $index => $entry){
            $report_date = new DateTime($data['en_report_date'][$index]);
            $item = $data['en_item'][$index];
            $quantity = $data['en_qty'][$index];
            $price = $data['en_price'][$index];
            $remarks = $data['en_remarks'][$index];
            
            $entry = new PulloutEntry();
            $entry->setItem($item)
                ->setNotes($remarks)
                ->setReportDate($report_date)
                ->setQuantity($quantity)
                ->setPrice($price);
            
            $o->addEntry($entry);
        }
        }
    }
    
    protected function padFormParams(&$params, $object = null)
    {
        $object->setWarehouse($this->getUser()->getWarehouse());
    
        $params['status_opts'] = array('Draft'=>'Draft',
                                    'Sent'=>'Sent');
    }
    
    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Pull Out No.','getCode','code'),
            $grid->newColumn('Date Created','getDateCreateFormatted','date_create'),
            $grid->newColumn('Created By','getName','name','u'),
            $grid->newColumn('Status','getStatus','status'),
        );
    }
    
    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('u', 'user_create', 'getUserCreate'),
        );
    }


    protected function getObjectLabel($obj)
    {
        return $obj->getCode();
    }

    protected function newBaseClass()
    {
        return new Pullout();
    }
    
    protected function hookPostSave($obj,$is_new = false) 
    {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateCode();
            $em->persist($obj);
            $em->flush();
        }
       
    }
}
