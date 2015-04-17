<?php

namespace Fareast\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\ManufacturingBundle\Entity\DailyConsumptions;
use Catalyst\ManufacturingBundle\Entity\ShiftReports;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class ProductionController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'feac_mfg_prod_cal';
        $this->title = 'Production Calendar';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        return new DailyConsumptions();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    public function indexAction()
    {
        $this->title = 'Production Calendar';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $today = new DateTime();
        $params['today'] = $today->format('Ymd');    

        $data = $this->findDailyConsumption($today->format("Ymd"));

        $params['consumption'] = $data;

        $em = $this->getDoctrine()->getManager();

        $date_from = new DateTime($today->format('Ymd')." 00:00:00");
        $date_to = new DateTime($today->format('Ymd')." 23:59:59");

        $query = 'SELECT s FROM CatalystManufacturingBundle:ShiftReports s 
        WHERE s.date_create >= :date_from AND s.date_create <= :date_to';
        $data = $em->createQuery($query)
            ->setMaxResults(3)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->getResult();

        $params['shift_reports'] = $data;

        return $this->render('FareastManufacturingBundle:Production:index.html.twig', $params);
    }

    public function dailyConsumptionAjaxAction($date)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->findDailyConsumption($date);


        $date_from = new DateTime($date." 00:00:00");
        $date_to = new DateTime($date." 23:59:59");

        $query = 'SELECT s FROM CatalystManufacturingBundle:ShiftReports s 
        WHERE s.date_create >= :date_from AND s.date_create <= :date_to';
        $shift_reports = $em->createQuery($query)
            ->setMaxResults(3)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->getResult();

        $shift_reports_id = array();
        $shift_reports_fermentations = array();
        $shift_reports_biogas = array();
        $shift_reports_bunker = array();
        $shift_reports_shift = array();
        $shift_reports_user = array();

        foreach ($shift_reports as $s)
        {
            array_push($shift_reports_id, $s->getID());
            array_push($shift_reports_fermentations, $s->getFermentation());
            array_push($shift_reports_biogas, $s->getBiogasProduced());
            array_push($shift_reports_bunker, $s->getBunker());
            array_push($shift_reports_shift, $s->getShift());
            array_push($shift_reports_user, $s->getUserCreate()->getName());
        }

        if ($data != null)
        {
            $consumption = $data;
            $running_balance = array(
                    'balance_mollases' => $consumption->getMolRunningBalance(),
                    'balance_bunker' => $consumption->getBunkerRunningBalance(),
                    'balance_acid' => $consumption->getSulRunningBalance(),
                    'balance_soda' => $consumption->getSodaRunningBalance(),
                    'balance_urea' => $consumption->getUreaRunningBalance(),
                    'balance_salt' => $consumption->getSaltRunningBalance(),
                    'shift_reports_id' => $shift_reports_id,
                    'shift_reports_fermentations' => $shift_reports_fermentations,
                    'shift_reports_biogas' => $shift_reports_biogas,
                    'shift_reports_bunker' => $shift_reports_bunker,
                    'shift_reports_shift' => $shift_reports_shift,
                    'shift_reports_user' => $shift_reports_user,
                );
        }
        else
        {
            $running_balance = array(
                    'balance_mollases' => 0,
                    'balance_bunker' => 0,
                    'balance_acid' => 0,
                    'balance_soda' => 0,
                    'balance_urea' => 0,
                    'balance_salt' => 0,
                    'shift_reports_id' => '',
                    'shift_reports_fermentations' => '',
                    'shift_reports_biogas' => '',
                    'shift_reports_bunker' => '',
                    'shift_reports_shift' => '',
                    'shift_reports_user' => '',
                );
        }

        $resp = new Response(json_encode($running_balance));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }


    public function dailyConsumptionAction($date)
    {
        $this->title = 'Daily Consumption';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $em = $this->getDoctrine()->getManager();

        $data = $this->findDailyConsumption($date);

        $date_time_now = new DateTime();
        $time_now = $date_time_now->format('H:i:s');

        if ($data == null)
        {
            $consumption = new DailyConsumptions();
            $consumption->setDateCreate(new DateTime($date." ".$time_now))
                ->setMolBeginningBalance(0)
                ->setMolPurchases(0)
                ->setMolPumpedMDT(0)
                ->setMolRunningBalance(0)
                ->setMolPondo(0)
                ->setMolProductionGal(0)
                ->setMolProductionTon(0)
                ->setMolTsai(0)
                ->setMolBrix(0)
                ->setBunkerBeginningBalance(0)
                ->setBunkerPurchase(0)
                ->setBunkerConsumption(0)
                ->setBunkerRunningBalance(0)
                ->setSulBeginningBalance(0)
                ->setSulPurchase(0)
                ->setSulConsumption(0)
                ->setSulRunningBalance(0)
                ->setSodaBeginningBalance(0)
                ->setSodaPurchase(0)
                ->setSodaConsumption(0)
                ->setSodaRunningBalance(0)
                ->setUreaBeginningBalance(0)
                ->setUreaPurchase(0)
                ->setUreaConsumption(0)
                ->setUreaRunningBalance(0)
                ->setSaltBeginningBalance(0)
                ->setSaltPurchase(0)
                ->setSaltConsumption(0)
                ->setSaltRunningBalance(0);

            $em->persist($consumption);
            $em->flush();
        }
        else
        {
            $consumption = $data;
        }


        $params['consumption'] = $consumption;

        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }

    public function dailyConsumptionSubmitAction($date)
    {
        $this->title = 'Daily Consumption';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $em = $this->getDoctrine()->getManager();

        $consumption = $this->findDailyConsumption($date); 

        $data = $this->getRequest()->request->all();    
        $consumption->setMolBeginningBalance($data['begin-mol'])
            ->setMolPurchases($data['purchase'])
            ->setMolPumpedMDT($data['pumped'])
            ->setMolRunningBalance($data['run-mol'])
            ->setMolPondo($data['pondo'])
            ->setMolProductionGal($data['mol-gal'])
            ->setMolProductionTon($data['mol-ton'])
            ->setMolTsai($data['tsai'])
            ->setMolBrix($data['brix'])

            ->setBunkerBeginningBalance($data['begin-bunk'])
            ->setBunkerPurchase($data['bunk-pur'])
            ->setBunkerConsumption($data['consumed'])
            ->setBunkerRunningBalance($data['run-bunk'])

            ->setSulBeginningBalance($data['begin-acid'])
            ->setSulPurchase($data['acid-purchase'])
            ->setSulConsumption($data['acid-consumed'])
            ->setSulRunningBalance($data['run-acid'])

            ->setSodaBeginningBalance($data['begin-soda'])
            ->setSodaPurchase($data['soda-purchase'])
            ->setSodaConsumption($data['soda-consumed'])
            ->setSodaRunningBalance($data['run-soda'])

            ->setUreaBeginningBalance($data['begin-urea'])
            ->setUreaPurchase($data['urea-purchase'])
            ->setUreaConsumption($data['urea-consumed'])
            ->setUreaRunningBalance($data['run-urea'])

            ->setSaltBeginningBalance($data['begin-salt'])
            ->setSaltPurchase($data['salt-purchase'])
            ->setSaltConsumption($data['salt-consumed'])
            ->setSaltRunningBalance($data['run-salt']);

        $em->persist($consumption);
        $em->flush();


        $this->addFlash('success', 'Daily Consumption has been updated.'); 
        $params['consumption'] = $consumption;
        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }

    protected function findDailyConsumption($date)
    {
        $em = $this->getDoctrine()->getManager();

        $date_from = new DateTime($date." 00:00:00");
        $date_to = new DateTime($date." 23:59:59");

        $query = 'SELECT d FROM CatalystManufacturingBundle:DailyConsumptions d 
        WHERE d.date_create >= :date_from AND d.date_create <= :date_to';
        $data = $em->createQuery($query)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->getResult();

        if ($data != null)
        {
            return $data[0];
        }
        else
        {
            return null;
        }
    }

    public function shiftReportAction($date)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $params['shift_opts'] = [
            '7:00 to 15:00' => '7:00 to 15:00',
            '15:00 to 23:00' => '15:00 to 23:00',
            '23:00 to 7:00' => '23:00 to 7:00',
        ];

        return $this->render('FareastManufacturingBundle:Production:shift-report.html.twig', $params);
    }

    public function shiftReportSubmitAction($date)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $em = $this->getDoctrine()->getManager();

        $data = $this->getRequest()->request->all();

        $today = new DateTime();
        $shift_reports = new ShiftReports();
        $shift_reports->setDateCreate(new DateTime($date." ".$today->format('h:i:s')))
            ->setUserCreate($this->getUser())
            ->setShift($data['shift'])
            ->setFineAlcohol($data['fine-alcohol'])
            ->setHeadsAlcohol($data['heads-alcohol'])
            ->setAlcoholProduced($data['produced'])
            ->setPPT($data['ppt'])
            ->setPROOF($data['proof'])
            ->setColumnOperator($data['column-operator'])
            ->setBeerAlcohol($data['beer-alcohol'])
            ->setFermentation($data['fermentation'])
            ->setMixer($data['mixer'])
            ->setBiogasProduced($data['biogas'])
            ->setGPLA($data['gpla'])
            ->setBiogasBunker($data['biogas-bunker'])
            ->setBiogasSteam($data['steam'])
            ->setVFA($data['vfa'])
            ->setCOD($data['cod'])
            ->setSlops($data['slops'])
            ->setSampling($data['sampling'])
            ->setVolume($data['volume'])
            ->setTemperature($data['temp'])
            ->setPH($data['ph'])
            ->setBiogasOperator($data['biogas-operator'])
            ->setHusk($data['husk'])
            ->setBunker($data['bunker'])
            ->setLOALOB($data['loa-lob'])
            ->setSteamProduced($data['steam-produced'])
            ->setProducedSteam($data['produced-steam'])
            ->setLOAHusk($data['loa-husk'])
            ->setHuskLOA($data['husk-loa'])
            ->setSteamLOA($data['steam-loa'])
            ->setSteamHusk($data['steam-husk'])
            ->setBoiler($data['boiler'])
            ->setBoilerOperator($data['boiler-operator']);

        $em->persist($shift_reports);
        $em->flush();

        $params['shift_opts'] = [
            '7:00 to 15:00' => '7:00 to 15:00',
            '15:00 to 23:00' => '15:00 to 23:00',
            '23:00 to 7:00' => '23:00 to 7:00',
        ];

        $this->addFlash('success', 'Shift Report has been created.'); 

        return $this->render('FareastManufacturingBundle:Production:shift-report.html.twig', $params);
    }

    public function shiftReportEditAction($id)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $em = $this->getDoctrine()->getManager();

        $shift_report = $em->getRepository('CatalystManufacturingBundle:ShiftReports')->find($id);

        $params['shift_report'] = $shift_report;

        $today = new DateTime();
        $params['today'] = $today->format('Ymd');


        $params['shift_opts'] = [
            '7:00 to 15:00' => '7:00 to 15:00',
            '15:00 to 23:00' => '15:00 to 23:00',
            '23:00 to 7:00' => '23:00 to 7:00',
        ];

        return $this->render('FareastManufacturingBundle:Production:shift-report-edit.html.twig', $params);
    }


    public function shiftReportEditSubmitAction($id)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $data = $this->getRequest()->request->all();

        $em = $this->getDoctrine()->getManager();


        $shift_report = $em->getRepository('CatalystManufacturingBundle:ShiftReports')->find($id);

        $shift_report->setShift($data['shift'])
            ->setFineAlcohol($data['fine-alcohol'])
            ->setHeadsAlcohol($data['heads-alcohol'])
            ->setAlcoholProduced($data['produced'])
            ->setPPT($data['ppt'])
            ->setPROOF($data['proof'])
            ->setColumnOperator($data['column-operator'])
            ->setBeerAlcohol($data['beer-alcohol'])
            ->setFermentation($data['fermentation'])
            ->setMixer($data['mixer'])
            ->setBiogasProduced($data['biogas'])
            ->setGPLA($data['gpla'])
            ->setBiogasBunker($data['biogas-bunker'])
            ->setBiogasSteam($data['steam'])
            ->setVFA($data['vfa'])
            ->setCOD($data['cod'])
            ->setSlops($data['slops'])
            ->setSampling($data['sampling'])
            ->setVolume($data['volume'])
            ->setTemperature($data['temp'])
            ->setPH($data['ph'])
            ->setBiogasOperator($data['biogas-operator'])
            ->setHusk($data['husk'])
            ->setBunker($data['bunker'])
            ->setLOALOB($data['loa-lob'])
            ->setSteamProduced($data['steam-produced'])
            ->setProducedSteam($data['produced-steam'])
            ->setLOAHusk($data['loa-husk'])
            ->setHuskLOA($data['husk-loa'])
            ->setSteamLOA($data['steam-loa'])
            ->setSteamHusk($data['steam-husk'])
            ->setBoiler($data['boiler'])
            ->setBoilerOperator($data['boiler-operator']);

        $em->persist($shift_report);
        $em->flush();

        $params['shift_report'] = $shift_report;

        $this->addFlash('success', 'Shift Report has been Updated.');             
        
        
        $params['shift_opts'] = [
            '7:00 to 15:00' => '7:00 to 15:00',
            '15:00 to 23:00' => '15:00 to 23:00',
            '23:00 to 7:00' => '23:00 to 7:00',
        ];

        return $this->render('FareastManufacturingBundle:Production:shift-report-edit.html.twig', $params);
    }

    public function printPDFAction($date)
    {
        $pdf = $this->get('catalyst_pdf');
        $pdf->newPdf('page');

        $date_picked = new DateTime($date);

        $em = $this->getDoctrine()->getManager();

        $date_from = new DateTime($date." 00:00:00");
        $date_to = new DateTime($date." 23:59:59");

        $query = 'SELECT s FROM CatalystManufacturingBundle:ShiftReports s 
        WHERE s.date_create >= :date_from AND s.date_create <= :date_to';
        $shift_reports = $em->createQuery($query)
            ->setMaxResults(3)
            ->setParameter('date_from', $date_from)
            ->setParameter('date_to', $date_to)
            ->getResult();

        $data = $this->findDailyConsumption($date);

        $params['consumption'] = $data;
        $params['date'] = $date_picked;
        $params['shift_reports'] = $shift_reports;

        $html = $this->render('FareastManufacturingBundle:Production:pdf/pdf.html.twig', $params);

        return $pdf->printPdf($html->getContent());
    }

}