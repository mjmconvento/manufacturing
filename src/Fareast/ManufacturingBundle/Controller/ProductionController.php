<?php

namespace Fareast\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Fareast\ManufacturingBundle\Entity\DailyConsumption;
use Fareast\ManufacturingBundle\Entity\ShiftReport;
use Catalyst\InventoryBundle\Entity\Entry;
use Catalyst\InventoryBundle\Entity\Transaction;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\CoreBundle\Template\Controller\TrackUpdate;
use Catalyst\CoreBundle\Template\Controller\HasGeneratedID;
use DateTime;

class ProductionController extends CrudController
{
    use TrackCreate;
    use TrackUpdate;
    use HasGeneratedID;

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

        $mfg = $this->get('fareast_manufacturing');
        $data = $mfg->findDailyConsumption($today->format("Ymd"));
        $params['shift_reports'] = $mfg->getShiftReports($today->format('Ymd'));
        $params['consumption'] = $data;

        return $this->render('FareastManufacturingBundle:Production:index.html.twig', $params);
    }

    public function dailyConsumptionAction($date)
    {
        $this->title = 'Daily Consumption';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');
        $em = $this->getDoctrine()->getManager();

        $mfg = $this->get('fareast_manufacturing');
        $data = $mfg->findDailyConsumption($date);

        $date_time_now = new DateTime();

        if ($data == null)
        {
            $consumption = new DailyConsumption();
            $consumption->setDateCreate(new DateTime())
                ->setDateProduced(new DateTime($date));

            $this->updateTrackCreate($consumption, $data, 'true');
            $this->updateTrackUpdate($consumption, $data);

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

        $this->padFormParams($params);

        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }

    public function dailyConsumptionSubmitAction($date)
    {
        $this->title = 'Daily Consumption';
        $params = $this->getViewParams('', 'feac_mfg_prod_cal');

        $em = $this->getDoctrine()->getManager();

        $mfg = $this->get('fareast_manufacturing');
        $consumption = $mfg->findDailyConsumption($date); 

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
            
        $this->updateTrackUpdate($consumption, $data);

        $em->persist($consumption);
        $em->flush();

        $this->addFlash('success', 'Daily Consumption has been updated.'); 
        $params['consumption'] = $consumption;
        $date = new DateTime($date);
        $params['date'] = $date;

        $this->padFormParams($params);

        return $this->render('FareastManufacturingBundle:Production:daily-consumption.html.twig', $params);
    }

    protected function padFormParams(&$params, $o = null)
    {
        $em = $this->getDoctrine()->getManager();

        // main warehouse account
        $config = $this->get('catalyst_configuration');
        $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($config->get('catalyst_warehouse_production_tank'));
        $prod_acc = $wh->getInventoryAccount();
    
        $params['mollases_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_MOLLASES);
        $params['bunker_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_BUNKER);
        $params['sulfuric_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_SULFURIC_ACID);
        $params['caustic_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_CAUSTIC_SODA);
        $params['urea_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_UREA);
        $params['salt_count'] = $this->getProductStockCount($prod_acc, DailyConsumption::PROD_SALT);

        return $params;
    }

    protected function getProductStockCount($wh_inv_acc, $product_name)
    {
        $inv = $this->get('catalyst_inventory');
        $prod = $inv->findProductByName($product_name);
        $product_count = $inv->getStockCount($wh_inv_acc, $prod);
        return $product_count;
    }

    protected function addEntries($product, $qty, $transaction)
    {
        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('catalyst_inventory');
        $config = $this->get('catalyst_configuration');

        // Getting warehouse inventory account
        $wh = $inv->findWarehouse($config->get('catalyst_warehouse_stock_adjustment'));
        $stock_adj_acc = $wh->getInventoryAccount();

        // Getting warehouse production account
        $wh = $inv->findWarehouse($config->get('catalyst_warehouse_production_tank'));
        $prod_acc = $wh->getInventoryAccount();

        // entry for adjustment
        $stock_adj_entry = new Entry();
        $stock_adj_entry->setInventoryAccount($stock_adj_acc)
            ->setProduct($product)
            ->setCredit($qty);

        $transaction->addEntry($stock_adj_entry);

        // entry for warehouse
        $prod_entry = new Entry();
        $prod_entry->setInventoryAccount($prod_acc)
            ->setProduct($product)
            ->setDebit($qty);

        $transaction->addEntry($prod_entry);
    }

    protected function addShiftReportEntries($transaction, $shift_reports)
    {

        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        $config = $this->get('catalyst_configuration');

        $heads_alcohol = $inv->findProductByName(ShiftReport::PROD_HEADS_ALCOHOL);
        $fine_alcohol = $inv->findProductByName(ShiftReport::PROD_FINE_ALCOHOL);

        $wh = $inv->findWarehouse($config->get('catalyst_warehouse_stock_adjustment'));
        $stock_adj_acc = $wh->getInventoryAccount();

        $wh = $inv->findWarehouse($config->get('catalyst_warehouse_main'));
        $wh_acc = $wh->getInventoryAccount();


        $heads_alcohol_total = 0;
        $fine_alcohol_total = 0;

        foreach ($shift_reports as $s)
        {
            $heads_alcohol_total += intval($s->getHeadsAlcohol());
            $fine_alcohol_total += intval($s->getFineAlcohol());
        }


        if ($heads_alcohol_total != 0)
        {
            // entry for adjustment
            $stock_adj_entry = new Entry();
            $stock_adj_entry->setInventoryAccount($stock_adj_acc)
                ->setProduct($heads_alcohol)
                ->setCredit($heads_alcohol_total);

            $transaction->addEntry($stock_adj_entry);

            // entry for warehouse
            $wh_entry = new Entry();
            $wh_entry->setInventoryAccount($wh_acc)
                ->setProduct($heads_alcohol)
                ->setDebit($heads_alcohol_total);

            $transaction->addEntry($wh_entry);
        }


        if ($fine_alcohol_total != 0)
        {
            // entry for adjustment
            $stock_adj_entry = new Entry();
            $stock_adj_entry->setInventoryAccount($stock_adj_acc)
                ->setProduct($fine_alcohol)
                ->setCredit($fine_alcohol_total);

            $transaction->addEntry($stock_adj_entry);

            // entry for warehouse
            $wh_entry = new Entry();
            $wh_entry->setInventoryAccount($wh_acc)
                ->setProduct($fine_alcohol)
                ->setDebit($fine_alcohol_total);

            $transaction->addEntry($wh_entry);
        }
    }


    public function printPDFAction($date)
    {
        $pdf = $this->get('catalyst_pdf');
        $pdf->newPdf('page_legal');

        $date_picked = new DateTime($date);

        $mfg = $this->get('fareast_manufacturing');
        $data = $mfg->findDailyConsumption($date);
        $em = $this->getDoctrine()->getManager();

        $params['consumption'] = $data;
        $params['date'] = $date_picked;
        $params['shift_reports'] = $mfg->getShiftReports($date);

        if ($data != NULL)
        {
            $old_generated_status = $data->getIsGenerated();
            $data->setIsGenerated(true);
            $em->persist($data);
        }


        if ($data != NULL and $old_generated_status == NULL)
        {
            $transaction = new Transaction;
            $transaction->setUserCreate($this->getUser())
                ->setDateCreate(new DateTime())
                ->setDescription('Daily Consumption');
            $em->persist($transaction);

            $inv = $this->get('catalyst_inventory');
            $config = $this->get('catalyst_configuration');

            // Getting warehouse inventory account
            $wh = $inv->findWarehouse($config->get('catalyst_warehouse_main'));
            $wh_acc = $wh->getInventoryAccount();

            // Getting warehouse production account
            $wh = $inv->findWarehouse($config->get('catalyst_warehouse_production_tank'));
            $prod_acc = $wh->getInventoryAccount();

            $mollases = $inv->findProductByName(DailyConsumption::PROD_MOLLASES);
            $bunker = $inv->findProductByName(DailyConsumption::PROD_BUNKER);
            $sulfur = $inv->findProductByName(DailyConsumption::PROD_SULFURIC_ACID);
            $caustic = $inv->findProductByName(DailyConsumption::PROD_CAUSTIC_SODA);
            $urea = $inv->findProductByName(DailyConsumption::PROD_UREA);
            $salt = $inv->findProductByName(DailyConsumption::PROD_SALT);

            
            // MOLASSES
            $added_qty = bcsub($data->getMolRunningBalance(), $data->getMolBeginningBalance() ,2);
            if($data->getMolRunningBalance() != $data->getMolBeginningBalance())
            {
                $this->addEntries($mollases, $added_qty, $transaction);
            }

            // BUNKER
            // updating bunker warehouse stock
            $added_qty = bcsub($data->getBunkerRunningBalance(), $data->getBunkerBeginningBalance() ,2);
            if($data->getBunkerRunningBalance() != $data->getBunkerBeginningBalance())
            {
                $this->addEntries($bunker, $added_qty, $transaction);
            }

            // SULFUR
            $added_qty = bcsub($data->getSulRunningBalance(), $data->getSulBeginningBalance() ,2);
            if($data->getSulRunningBalance() != $data->getSulBeginningBalance())
            {
                $this->addEntries($sulfur, $added_qty, $transaction);
            }

            // CAUSTIC
            $added_qty = bcsub($data->getSodaRunningBalance(), $data->getSodaBeginningBalance() ,2);
            if($data->getSodaRunningBalance() != $data->getSodaBeginningBalance())
            {
                $this->addEntries($caustic, $added_qty, $transaction);
            }

            // UREA
            $added_qty = bcsub($data->getUreaRunningBalance(), $data->getUreaBeginningBalance() ,2);
            if($data->getUreaRunningBalance() != $data->getUreaBeginningBalance())
            {
                $this->addEntries($urea, $added_qty, $transaction);
            }

            // SALT
            $added_qty = bcsub($data->getSaltRunningBalance(), $data->getSaltBeginningBalance() ,2);
            if($data->getSaltRunningBalance() != $data->getSaltBeginningBalance())
            {
                $this->addEntries($salt, $added_qty, $transaction);
            }

            $shift_reports = $mfg->getShiftReports($date);
            $this->addShiftReportEntries($transaction, $shift_reports);

            $inv->persistTransaction($transaction);
        }

        $em->flush();
        $html = $this->render('FareastManufacturingBundle:Production:pdf/pdf.html.twig', $params);
        return $pdf->printPdf($html->getContent());
    }

  


    public function dailyConsumptionAjaxAction($date)
    {
        $em = $this->getDoctrine()->getManager();


        $mfg = $this->get('fareast_manufacturing');
        $data = $mfg->findDailyConsumption($date);

        $shift_reports = $mfg->getShiftReports($date);

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

        if ($data != null and $shift_reports != null)
        {
            $consumption = $data;
            $running_balance = array(
                    'balance_mollases' => $consumption->getMolRunningBalance().' gal',
                    'balance_bunker' => $consumption->getBunkerRunningBalance().' L',
                    'balance_acid' => $consumption->getSulRunningBalance().' gal',
                    'balance_soda' => $consumption->getSodaRunningBalance().' L',
                    'balance_urea' => $consumption->getUreaRunningBalance().' bags',
                    'balance_salt' => $consumption->getSaltRunningBalance().' bags',
                    'shift_reports_id' => $shift_reports_id,
                    'shift_reports_fermentations' => $shift_reports_fermentations,
                    'shift_reports_biogas' => $shift_reports_biogas,
                    'shift_reports_bunker' => $shift_reports_bunker,
                    'shift_reports_shift' => $shift_reports_shift,
                    'shift_reports_user' => $shift_reports_user,
                );
        }

        elseif ($data == null and $shift_reports != null)
        {
            $running_balance = array(
                    'balance_mollases' => '0 gal',
                    'balance_bunker' => '0 L',
                    'balance_acid' => '0 gal',
                    'balance_soda' => '0 L',
                    'balance_urea' => '0 bags',
                    'balance_salt' => '0 bags',
                    'shift_reports_id' => $shift_reports_id,
                    'shift_reports_fermentations' => $shift_reports_fermentations,
                    'shift_reports_biogas' => $shift_reports_biogas,
                    'shift_reports_bunker' => $shift_reports_bunker,
                    'shift_reports_shift' => $shift_reports_shift,
                    'shift_reports_user' => $shift_reports_user,
                );
        }
        
        elseif ($data != null and $shift_reports == null)
        {
            $consumption = $data;
            $running_balance = array(
                    'balance_mollases' => $consumption->getMolRunningBalance().' gal',
                    'balance_bunker' => $consumption->getBunkerRunningBalance().' L',
                    'balance_acid' => $consumption->getSulRunningBalance().' gal',
                    'balance_soda' => $consumption->getSodaRunningBalance().' L',
                    'balance_urea' => $consumption->getUreaRunningBalance().' bags',
                    'balance_salt' => $consumption->getSaltRunningBalance().' bags',
                    'shift_reports_id' => '',
                    'shift_reports_fermentations' => '',
                    'shift_reports_biogas' => '',
                    'shift_reports_bunker' => '',
                    'shift_reports_shift' => '',
                    'shift_reports_user' => '',
                );
        }

        else
        {
            $running_balance = array(
                    'balance_mollases' => '0 gal',
                    'balance_bunker' => '0 L',
                    'balance_acid' => '0 gal',
                    'balance_soda' => '0 L',
                    'balance_urea' => '0 bags',
                    'balance_salt' => '0 bags',
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


}