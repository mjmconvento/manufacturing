<?php

namespace Fareast\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\ManufacturingBundle\Entity\ShiftReports;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class ShiftReportsController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'feac_mfg_shift_rep_new';
        $this->title = 'Shift Reports';
    }

    protected function newBaseClass()
    {
        return new ShiftReports();
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    public function shiftReportAction($date)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $this->padFormParams($params);

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

        $this->padFormParams($params);


        $this->addFlash('success', 'Shift Report has been created.'); 

        return $this->render('FareastManufacturingBundle:Production:shift-report.html.twig', $params);
    }

    public function shiftReportEditAction($id)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $params['shift_report'] = $this->findShiftReport($id);

        $this->padFormParams($params);

        return $this->render('FareastManufacturingBundle:Production:shift-report-edit.html.twig', $params);
    }


    public function shiftReportEditSubmitAction($id)
    {
        $this->title = 'Create Shift Report';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $data = $this->getRequest()->request->all();

        $em = $this->getDoctrine()->getManager();
        $shift_report = $this->findShiftReport($id);

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
        
        $this->padFormParams($params);

        return $this->render('FareastManufacturingBundle:Production:shift-report-edit.html.twig', $params);
    }

    protected function padFormParams(&$params, $obj = null)
    {
        $params['shift_opts'] = [
            '7:00 to 15:00' => '7:00 to 15:00',
            '15:00 to 23:00' => '15:00 to 23:00',
            '23:00 to 7:00' => '23:00 to 7:00',
        ];

        $today = new DateTime();
        $params['today'] = $today->format('Ymd');

        return $params;
    }

    protected function findShiftReport($id)
    {
        $em = $this->getDoctrine()->getManager();
        $shift_report = $em->getRepository('FareastManufacturingBundle:ShiftReports')->find($id);

        return $shift_report;
    }

}