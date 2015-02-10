<?php

namespace Serenitea\InventoryOrderBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;

class RequestListController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'serenitea_rlist';
        $this->title = 'Request List';

        $this->list_title = 'Request Lists';
        $this->list_type = 'static';
    }
    public function indexAction()
    {
        $this->title = 'Request Lists';
        $params = $this->getViewParams('', 'serenitea_rlist_index');
        return $this->render('SereniteaInventoryOrderBundle:Request:index.html.twig', $params);
    }
    
    public function editAction(){
        $this->title = 'Requested Order';
        $params = $this->getViewParams('', 'serenitea_rlist_edit');
        return $this->render('SereniteaInventoryOrderBundle:Request:edit.html.twig', $params);
    }

    protected function getObjectLabel($object) {
        
    }

    protected function newBaseClass() {
        
    }

}
