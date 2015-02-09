<?php

namespace Catalyst\SalesBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\SalesBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\ValidationException;
use DateTime;


use Catalyst\CoreBundle\Model\Controller\TrackCreate;
use Catalyst\ContactBundle\Model\Controller\Contact;

class CustomerController extends CrudController
{
    use TrackCreate;
    use Contact;
    
    public function __construct()
    {

        $this->filter_url = 'leather_report_claimed_sum_filter';
        $this->menu_url = 'leather_report_claimed_sum_index';
        $this->route_prefix = 'cat_sales_cust';
        $this->title = 'Customer';
        $this->series_title = 'Claimed Item';
        $this->list_title = 'Customers';
        $this->list_type = 'dynamic';        
        $this->method_date = 'getDateIssue';
        $this->method_amount = 'getProductCount';

    }


    public function indexAction()
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'CatalystTemplateBundle::index.csv.html.twig';

        $params['list_title'] = $this->list_title;

        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }

    public function editFormAction($id)
    {
        $this->checkAccess($this->route_prefix . '.view');
        $this->filter_url = 'cat_sales_service_filter';

        $this->hookPreAction();
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository($this->repo)->find($id);

        $params = $this->getViewParams('Edit');
        $params['object'] = $obj;
        $params['o_label'] = $this->getObjectLabel($obj);

       // params
        $params['date_from'] = new DateTime('now');
        $params['date_to'] = new DateTime('now');

      
        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.edit');

        $this->padFormParams($params, $obj);

        return $this->render('CatalystTemplateBundle:Object:edit.html.twig', $params);
    }



    protected function newBaseClass()
    {
        return new Customer();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getDisplayName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Name', 'getDisplayName', 'last_name'),
            $grid->newColumn('Email', 'getEmail', 'email'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        if ($o->getID())
            $params['stock_cols'] = $this->getStockColumns();

        return $params;
    }

    protected function getStockColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Branch', 'getName', 'name', 'w'),
            $grid->newColumn('Job Order #', 'getCode', 'code' ),
            $grid->newColumn('Customer Item', 'getName', 'name', 'p' ),
            $grid->newColumn('Date Issued', 'getDateIssueFormat', 'date_issue' ),
            $grid->newColumn('Due Date (Client)', 'getDateNeedFormat', 'date_need' ),
            $grid->newColumn('Due Date (Repairman)', 'getDateNeedRepairmanFormat', 'date_need' ),
            $grid->newColumn('Date Claimed', 'getDateClaimedFormat', 'date_claimed' ),
            $grid->newColumn('Date Finished', 'getDateCompletedFormat', 'date_completed' ),
            $grid->newColumn('Types Of Services', 'getServices', 'users' ),
            $grid->newColumn('Service Price', 'getPrices', 'users' ),
        );
    }


    protected function update($o, $data, $is_new = false)
    {
        $o->setFirstName($data['first_name']);
        $o->setLastName($data['last_name']);
        $o->setMiddleName($data['middle_name']);
        $o->setSalutation($data['salutation']);
        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateContact($o, $data, $is_new);
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'name' => $o->getName(),
            'address' => $o->getAddress(),
            'contact_number' => $o->getContactNumber(),
            'email' => $o->getEmail(),
            'contact_person' => $o->getContactPerson(),
            'notes' => $o->getNotes(),
        );

        return $data;
    }

    public function ajaxAddAction()
    {
        // $data = $this->getRequest()->query->all();
        $this->hookPreAction();
        $data = array();
        try
        {
            $em = $this->getDoctrine()->getManager();
            $data = $this->getRequest()->request->all();

            $obj = $this->newBaseClass();

            $this->update($obj, $data, true);

            $em->persist($obj);
            $em->flush();

            $odata = $obj->toData();
            $this->logAdd($odata);

            $buildData = $this->buildData($obj);

            $data = array(
                'status' => array('success' => true, 'message' => $this->getObjectLabel($obj) . ' added successfully.'),
                'data' => $buildData,
            );
        }
        catch (ValidationException $e)
        {
            $data = array(
                'status' => array('success' => false, 'message' => $e->getMessage()),
                'data' => null,
            );
        }
        catch (DBALException $e)
        {
            $data = array(
                'status' => array('success' => false, 'message' => 'Database error encountered. Possible duplicate.'),
                'data' => null,
            );
        }
        $resp = new Response(json_encode($data));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    public function ajaxGetAllAction()
    {
        $this->hookPreAction();

        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository($this->repo)->findAll();

        $data = array();
        foreach($obj as $val)
        {
            array_push($data, $this->buildData($val));
        }

        $resp = new Response(json_encode($data));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    protected function getStockReport()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('select c.name, c.address, c.contact_number, c.email, c.contact_person from Catalyst\SalesBundle\Entity\Customer c Order by c.name asc');              
        return $query->getResult();
    }


    protected function setupHistoryGrid($id)
    {
        $grid = $this->get('catalyst_grid');
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();

        // limit to this warehouse's stock
        $fg = $grid->newFilterGroup();
        $fg->where("o.customer_id = :id")
            ->setParameter('id' , $id);

        // setup grid
        $gl = $grid->newLoader();
        $gl->processParams($data)
            ->setRepository('CatalystServiceBundle:ServiceOrder')
            ->addJoin($grid->newJoin('p', 'product', 'getProduct'))
            ->addJoin($grid->newJoin('w', 'warehouse', 'getWarehouse'))
            ->addJoin($grid->newJoin('e', 'entries', 'getEntries'))
            ->enableCountFilter()
            ->setQBFilterGroup($fg);

        // columns
        $stock_cols = $this->getStockColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }



    public function historyGridAction($id)
    {
        $gl = $this->setupHistoryGrid($id);
        $gres = $gl->load();

        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }


    protected function setupHistoryGrid2($id, $date_from, $date_to)
    {
        $grid = $this->get('catalyst_grid');
        $data = $this->getRequest()->query->all();
        $em = $this->getDoctrine()->getManager();

        $date_from = date("Y-m-d", strtotime($date_from));


        $date_to = date("Y-m-d", strtotime($date_to));

        // limit to this warehouse's stock
        $fg = $grid->newFilterGroup();
        $fg->where("o.customer_id = :id and o.date_issue >= :date_from and o.date_issue <= :date_to")
            ->setParameter('id' , $id)
            ->setParameter('date_from' , $date_from)
            ->setParameter('date_to' , $date_to);

        // setup grid
        $gl = $grid->newLoader();
        $gl->processParams($data)
            ->setRepository('CatalystServiceBundle:ServiceOrder')
            ->addJoin($grid->newJoin('p', 'product', 'getProduct'))
            ->addJoin($grid->newJoin('w', 'warehouse', 'getWarehouse'))
            ->addJoin($grid->newJoin('e', 'entries', 'getEntries'))
            ->enableCountFilter()
            ->setQBFilterGroup($fg);

        // columns
        $stock_cols = $this->getStockColumns();
        foreach ($stock_cols as $col)
            $gl->addColumn($col);

        return $gl;
    }

    public function historyGrid2Action($id, $date_from, $date_to)
    {
        $gl = $this->setupHistoryGrid2($id, $date_from, $date_to);
        $gres = $gl->load();

        $resp = new Response($gres->getJSON());
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }


    public function filterAction($id,$date_from,$date_to)
    {
        $this->checkAccess($this->route_prefix . '.view');
        $this->filter_url = 'cat_sales_service_filter';

        $this->hookPreAction();
        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository($this->repo)->find($id);

        $params = $this->getViewParams('Edit');
        $params['object'] = $obj;
        $params['o_label'] = $this->getObjectLabel($obj);


        $date_from = date("Y-m-d", strtotime($date_from));
        // $date_from2 = explode("-",$date_from);
        // $date_from3 = $date_from2[0]."-".$date_from2[1]."-".$date_from2[2];

        $date_to = date("Y-m-d", strtotime($date_to));
        // $date_to2 = explode("-",$date_to);
        // $date_to3 = $date_to2[0]."-".$date_to2[1]."-".$date_to2[2];

       // params
        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;

        // check if we have access to form
        $params['readonly'] = !$this->getUser()->hasAccess($this->route_prefix . '.edit');

        $this->padFormParams($params, $obj);

        return $this->render('CatalystSalesBundle:Customer:form2.html.twig', $params);
    }





}
