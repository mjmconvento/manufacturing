<?php

namespace Fareast\PurchasingBundle\Controller;

use Catalyst\PurchasingBundle\Controller\PurchaseOrderController as BaseController;
use Catalyst\PurchasingBundle\Entity\PurchaseOrder;
use Catalyst\PurchasingBundle\Entity\POEntry;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\ValidationException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class PurchaseOrderController extends BaseController
{    
    public function __construct()
    {
        $this->repo = 'CatalystPurchasingBundle:PurchaseOrder';
        parent::__construct();

    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('PO No.', 'getCode', 'code'),
            $grid->newColumn('Date Issued', 'getDateIssue', 'date_issue', 'o', array($this, 'formatDate')),
            // $grid->newColumn('Order Type', 'getName', 'first_name', 's'),            
            $grid->newColumn('Supplier', 'getSupplierName', 'supplier_id'),
            // $grid->newColumn('Terms', 'getStatusFormatted', 'status_id'),
            $grid->newColumn('Status','getStatus','status_id')  
        );
    }

    public function padFormParams(&$params, $po=null)
    {
        $pur = $this->get('catalyst_purchasing');
        
        $params['supp_opts'] = $pur->getSupplierOptions();
        $params['request_opts'] = $pur->getRequestOptions();
    }

    public function getPurchaseRequestAction($id = null)
    {
        
        $data = $this->getEntries($id);

        return new JsonResponse($data);
    }

    public function getEntries($id = null)
    {
        $em = $this->getDoctrine()->getManager();

        if($id != null and $id != 'null')
        {
            $query = $em->createQuery('SELECT pr.id as request, p.id as product ,p.name , p.uom, p.price_purchase , r.quantity
                FROM CatalystPurchasingBundle:PREntry r
                INNER JOIN r.product p
                INNER JOIN r.purchase_request pr
                WHERE r.purchase_request = :pr_id ')
                ->setParameter('pr_id', $id);        
        }
        return $query->getResult();
    }
}
