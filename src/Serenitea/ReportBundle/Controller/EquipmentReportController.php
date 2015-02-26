<?php

namespace Catalyst\ReportBundle\Controller;

use Catalyst\TemplateBundle\Model\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\UserBundle\Entity\User;
use Catalyst\UserBundle\Entity\Group;
use DateTime;


class EquipmentReportController extends BaseController
{
    public function indexAction($date_from = null , $date_to = null)
    {
        $this->title = 'Equipment Report';
        $this->print = 'catalyst_report_equipment_print';
        $this->csv = 'catalyst_report_equipment_csv';
        $this->filter = 'catalyst_report_equipment_summary_filter';

        // get params
        $params = $this->getViewParams('', 'catalyst_report_equipment_summary');

        $date_from = new DateTime($date_from);
        $date_to = new DateTime($date_to);
        $date_from = $date_from->format("Y-m-d");
        $date_to = $date_to->format("Y-m-d");

        $this->padFormParams($params, $date_from, $date_to);

        $params['date_from'] = $date_from;
        $params['date_to'] = $date_to;
        $params['title'] = $this->title;
        $params['print'] = $this->print;
        $params['csv'] = $this->csv;
        $params['filter'] = $this->filter;

        return $this->render('CatalystReportBundle:ProductGroupsReport:index.html.twig', $params);
    }


    protected function padFormParams(&$params, $date_from, $date_to)
    {
        $em = $this->getDoctrine()->getManager();
        $params['so'] = $this->fetchData($date_from, $date_to);
        $params['stock_cols'] = $this->getStockColumns();

        return $params;
    }


    protected function getStockColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Code', 'getCode', 'code' ),
            $grid->newColumn('Assigned To', 'getAssignedUsersText', 'user_id' ),
            $grid->newColumn('Date Issue', 'getDateIssue', 'date_issue' ),
            $grid->newColumn('Services', 'getServicesText', 'services' ),
            $grid->newColumn('Customer', 'getFullName', 'user_id', 'c' ),
            $grid->newColumn('Product', 'getCity', 'user_id', 'c' ),
            $grid->newColumn('Quantity', 'getNote', 'note' ),
        );
    }

    public function headers()
    {
        // csv headers
        $headers = [
            'Code',
            'Assiged To',            
            'Date_Issue',
            'Services',
            'Customer',
            'Product',
            'Quantity',
        ];
        return $headers;
    }


    public function printAction($date_from, $date_to)
    {       

        // fetch data
        $data = $this->fetchData($date_from, $date_to);

        $this->title = 'Equipment Report';
        $params = $this->getViewParams('', 'catalyst_report_equipment_summary');

        $params['grid_cols'] = $this->headers();
        $params['data'] = $data;

        return $this->render(
            'CatalystReportBundle:ProductGroupsReport:print.html.twig', $params);
    }   

    public function csvAction($date_from, $date_to)
    {
        // fetch data
        $data = $this->fetchData($date_from, $date_to);

        // filename generate
        $date = new DateTime();
        $filename = $date->format('Ymdis') . '.csv';

        // redirect file to stdout, store in output buffer and place in $csv
        $file = fopen('php://output', 'w');
        ob_start();

        $csv_headers = $this->headers();

        fputcsv($file, $csv_headers);

        // data
        $i=0;
        foreach ($data as $so)
        {
            // build data
            $arr_data = [
                $so->getServiceOrder()->getCode(),
                $so->getServiceOrder()->getAssignedUserstext(),
                $so->getServiceOrder()->getDateIssue()->format('m/d/Y'),
                $so->getServiceOrder()->getServicesText(),
                $so->getServiceOrder()->getCustomer()->getFullName(),
                $so->getProduct()->getName(),
                $so->getQuantity(),
            ];

            $i++;
            // output csv
            fputcsv($file, $arr_data);
        }
        fclose($file);
        $csv = ob_get_contents();
        ob_end_clean();


        // csv header
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->setContent($csv);

        return $response;
    }


    protected function fetchData($date_from, $date_to)
    {

        $equip_id = $this->get('catalyst_configuration')->get('catalyst_product_group_equipment_default');

        $em = $this->getDoctrine()->getManager();
        $query = 'select s from CatalystServiceBundle:SVConsumption s join s.product p join s.service_order o where o.date_issue >= :date_from and o.date_issue <= :date_to and p.prodgroup_id = :id';
        $data = $em->createQuery($query)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->setParameter('id', $equip_id)
            ->getResult();

        return $data;
    }


}
