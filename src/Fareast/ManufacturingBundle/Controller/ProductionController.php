<?php

namespace Fareast\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\ManufacturingBundle\Entity\DailyConsumption;
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
        return new DailyConsumption();
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
        $params['shift_reports'] = $this->getShiftReports($today->format('Ymd'));

        return $this->render('FareastManufacturingBundle:Production:index.html.twig', $params);
    }

    public function dailyConsumptionAjaxAction($date)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $this->findDailyConsumption($date);

        $shift_reports = $this->getShiftReports($date);

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

        if ($data == null)
        {
            $consumption = new DailyConsumption();
            $consumption->setDateCreate(new DateTime())
                ->setDateProduced(new DateTime($date))
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

        $date = new DateTime($date);
        $params['date'] = $date;
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
            ->setSaltRunningBalance($data['run-salt'])

            ->setElectricityFinal($data['electricity-final'])
            ->setElectricityBeginning($data['electricity-beginning'])
            ->setElectricityUsed($data['electricity-used'])
            ->setKuLOA($data['alcohol-kw'])

            ->setFermentationEfficiency($data['fermentation-efficiency'])
            ->setDistillationEfficiency($data['distillation-efficiency'])
            ->setOverallEfficiency($data['overall-efficiency'])
            ->setAverageAlcohol($data['average-alcohol'])

            ->setAlcoholBeginning($data['alcohol-beginning'])
            ->setAlcoholOut($data['alcohol-out'])
            ->setAldehydeBeginning($data['aldehyde-beginning'])
            ->setAldehydeOut($data['aldehyde-out'])

            ->setDirectLaborNo($data['direct-labor-no'])
            ->setMaintenanceNo($data['maintenance-no'])
            ->setSupportGroupNo($data['support-group-no'])
            ->setPlantManagersNo($data['plant-managers-no'])
            ->setGuardNo($data['guard-no'])
            ->setExtraNo($data['extra-no'])
            ->setDirectLaborMH($data['direct-labor-mh'])
            ->setMaintenanceMH($data['maintenance-mh'])
            ->setSupportGroupMH($data['support-group-mh'])
            ->setPlantManagersMH($data['plant-managers-mh'])
            ->setGuardMH($data['guard-mh'])
            ->setExtraMH($data['extra-mh']);

        $em->persist($consumption);
        $em->flush();

        $this->addFlash('success', 'Daily Consumption has been updated.'); 
        $params['consumption'] = $consumption;
        $date = new DateTime($date);
        $params['date'] = $date;

        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }

    protected function findDailyConsumption($date)
    {
        $em = $this->getDoctrine()->getManager();

        $query = 'SELECT d FROM FareastManufacturingBundle:DailyConsumption d 
        WHERE d.date_produced = :date_today';
        $data = $em->createQuery($query)
            ->setParameter('date_today', $date)
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

    public function printPDFAction($date)
    {
        $pdf = $this->get('catalyst_pdf');
        $pdf->newPdf('page');

        $date_picked = new DateTime($date);

        $data = $this->findDailyConsumption($date);

        $params['consumption'] = $data;
        $params['date'] = $date_picked;
        $params['shift_reports'] = $this->getShiftReports($date);

        $html = $this->render('FareastManufacturingBundle:Production:pdf/pdf.html.twig', $params);

        return $pdf->printPdf($html->getContent());
    }

    protected function getShiftReports($date)
    {
        $em = $this->getDoctrine()->getManager();

        $query = 'SELECT s FROM FareastManufacturingBundle:ShiftReport s 
        WHERE s.date_produced = :date_produced';
        $shift_reports = $em->createQuery($query)
            ->setMaxResults(3)
            ->setParameter('date_produced', $date)
            ->getResult();

        return $shift_reports;
    }    

}