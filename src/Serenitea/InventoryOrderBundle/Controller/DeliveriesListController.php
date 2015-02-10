<?php

namespace Serenitea\InventoryOrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class DeliveriesListController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'serenitea_dlist';
        $this->title = 'Request Item';

        $this->list_title = 'Request Items';
        $this->list_type = 'static';
    }
    public function indexAction()
    {
        $this->title = 'Deliveries List';
        $params = $this->getViewParams('', 'serenitea_dlist_index');
        return $this->render('SereniteaInventoryOrderBundle:Request:deliver.html.twig', $params);
    }
    
    public function viewAction()
    {
        $this->title = 'Delivery Receipt';
        $params = $this->getViewParams('', 'serenitea_dlist_view');
        return $this->render('SereniteaInventoryOrderBundle:Request:receipt.html.twig', $params);
    }
    
//    public function editAction(){
//        $this->title = 'Requested Order';
//        $params = $this->getViewParams('', 'serenitea_rlist_edit');
//        return $this->render('SereniteaInventoryOrderBundle:Request:edit.html.twig', $params);
//    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}