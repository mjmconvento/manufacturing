<?php

namespace Catalyst\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ServiceTask;
use Catalyst\ValidationException;
use Catalyst\InventoryBundle\Model\Gallery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManager;


use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\CoreBundle\Template\Controller\TrackUpdate;
use Catalyst\CoreBundle\Template\Controller\HasGeneratedID;
use Catalyst\CoreBundle\Template\Controller\HasName;


class ProductController extends CrudController
{
    use TrackCreate;
    use TrackUpdate;
    use HasGeneratedID;
    use HasName;

    public function __construct()
    {
        $this->route_prefix = 'cat_inv_prod';
        $this->title = 'Product / Service';

        $this->list_title = 'Products / Services';
        $this->list_type = 'dynamic';
    }


    protected function newBaseClass()
    {
        return new Product();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getName();
    }

    protected function getGridJoins()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newJoin('pg', 'prodgroup', 'getProductGroup'),
        );
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('SKU', 'getSKU', 'sku'),
            $grid->newColumn('Name', 'getName', 'name'),
            $grid->newColumn('Unit', 'getUnitOfMeasure', 'uom'),
            $grid->newColumn('Group', 'getName', 'name', 'pg'),
        );
    }

    protected function padListParams(&$params)
    {
        $filter = $this->getRequest()->get('type');
        if ($filter == null)
            $params['filter_type'] = '';
        else
            $params['filter_type'] = $filter;

        $params['type_opts'] = array(
            0 => 'All Types',
            Product::TYPE_RAW_MATERIAL => 'Raw Material',
            Product::TYPE_FINISHED_GOOD => 'Finished Good',
            Product::TYPE_INVENTORY => 'Inventory'
        );

    }

    public function indexAction()
    {
        $this->checkAccess($this->route_prefix . '.view');

        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'CatalystInventoryBundle:Product:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        $this->padListParams($params);

        return $this->render($twig_file, $params);
    }

    protected function setupGridLoader()
    {
        $grid = $this->get('catalyst_grid');

        // get filter parameter
        $filter = $this->getRequest()->get('type');

        $loader = parent::setupGridLoader();

        // display only raw materials, finished goods and inventories
        $fg = $grid->newFilterGroup();
        if ($filter == null || $filter == 0)
        {
            // error_log('filter - null');
            $fg->where('o.type_id in (:type_ids)')
                ->setParameter('type_ids', array(
                    Product::TYPE_RAW_MATERIAL,
                    Product::TYPE_FINISHED_GOOD,
                    Product::TYPE_INVENTORY
                ));
        }
        else
        {            
            // error_log('filter - ' . $filter);
            $fg->where('o.type_id = :filter_id')
                ->setParameter('filter_id', $filter);

        }


        $loader->setQBFilterGroup($fg);

        return $loader;
    }

    protected function getFieldOptions($repo)
    {
        // brand
        $em = $this->getDoctrine()->getManager();
        $objects = $em->getRepository('CatalystInventoryBundle:' . $repo)->findAll();
        $opts = array();
        foreach ($objects as $o)
            $opts[$o->getId()] = $o->getName();

        return $opts;
    }

    protected function padFormParams(&$params, $prod = null)
    {

        $inv = $this->get('catalyst_inventory');
        $params['pg_opts'] = $inv->getProductGroupOptions();
        $brand_opts = array(0 => '[ Select Brand ]');

        $params['brand_opts'] = $brand_opts + $this->getFieldOptions('Brand');

        // images
        if ($prod->getID())
        {
            $gallery = $this->getGallery($prod->getID());
            $images = $gallery->getImages();
            $params['images'] = $images;
        }

        $params['type_opts'] = array(
            Product::TYPE_RAW_MATERIAL => 'Raw Material',
            Product::TYPE_FINISHED_GOOD => 'Finished Good',
            Product::TYPE_INVENTORY =>'Inventory'
        );


        return $params;
    }

    protected function validate($data, $type)
    {
        // SKU validation check
        if (empty($data['sku']))
            throw new ValidationException('Cannot leave SKU blank');

        // validate name
        if (empty($data['name']))
            throw new ValidationException('Cannot leave name blank');
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');
        
        $o->setName($data['name']);
        $o->setTypeID($data['type_id']);

        // unit of measure
        if (!empty($data['uom']))
            $o->setUnitOfMeasure($data['uom']);
        else
            $o->setUnitOfMeasure('');

        /*
        // service
        if (isset($data['flag_service']) && $data['flag_service'] == 1)
            $o->setFlagService();
        else
            $o->setFlagService(false);
        */

        // can sell
        if (isset($data['flag_sale']) && $data['flag_sale'] == 1)
            $o->setFlagSale();
        else
            $o->setFlagSale(false);

        // can purchase
        if (isset($data['flag_purchase']) && $data['flag_purchase'] == 1)
            $o->setFlagPurchase();
        else
            $o->setFlagPurchase(false);

        // perishable
        if (isset($data['flag_perishable']) && $data['flag_perishable'] == 1)
            $o->setFlagPerishable();
        else
            $o->setFlagPerishable(false);


        /*
        // boolean for sell and cost price acl
        $view_sell_price = $this->getUser()->hasAccess($this->route_prefix . '.view_sell_price');

        $view_cost_price = $this->getUser()->hasAccess($this->route_prefix . '.view_cost_price');
        */
        
        // prices
        $o->setPriceSale($data['price_sale']);
        $o->setPricePurchase($data['price_purchase']);


        
        // threshold values
        $o->setStockMin($data['stock_min']);
        $o->setStockMax($data['stock_max']);


        // product group
        $pg = $inv->findProductGroup($data['prodgroup_id']);
        if ($pg == null)
            throw new ValidationException('Could not find product group specified.');
        $o->setProductGroup($pg);

        // product brand
        $pb = $inv->findBrand($data['brand_id']);
        if ($pb != null)
            $o->setBrand($pb);        


        // sku check
        if ($o->getSKU() != $data['sku'])
        {
            $em = $this->getDoctrine()->getManager();
            $dupe = $em->getRepository('CatalystInventoryBundle:Product')->findOneBy(array('sku' => $data['sku']));
            if ($dupe != null)
                throw new ValidationException('Product with SKU ' . $data['sku'] . ' already exists.');
            $o->setSKU($data['sku']);
        }

        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateTrackUpdate($o, $data, $is_new);
        $this->updateHasGeneratedID($o, $data, $is_new);
        $this->updateHasName($o, $data, $is_new);
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'sku' => $o->getSKU(),
            'name' => $o->getName(),
            'uom' => $o->getUnitOfMeasure(),
            'flag_service' => $o->isService(),
            'flag_sale' => $o->canSell(),
            'flag_purchase' => $o->canPurchase(),
            'price_sale' => $o->getPriceSale(),
            'price_purchase' => $o->getPricePurchase(),
            'prodgroup_id' => $o->getProductGroup()->getID(),
        );

        return $data;
    }

    public function uploadAction($id)
    {
        // TODO: confirm that product exists

        // handle dropzone
        $file = $this->getRequest()->files->get('file');
        if ($file->getError())
            return new Response('Failed');

        // let our gallery lib handle it
        $gallery = $this->getGallery($id);
        $gallery->addImage($file);

        return new Response('Success');
    }

    protected function getGallery($id)
    {
        return new Gallery(__DIR__ . '/../../../../web/uploads/images', $id);
    }

    public function ajaxAddAction()
    {
        $data = $this->getRequest()->query->all();
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

    protected function findBy($filter = array())
    {
        $this->hookPreAction();

        $em = $this->getDoctrine()->getManager();
        $obj = $em->getRepository($this->repo)->findBy($filter);

        $data = array();
        foreach($obj as $val)
        {
            array_push($data, $this->buildData($val));
        }

        return $data;
    }

    public function ajaxGetByAction($id)
    {
        //$prodgroup = $this->get('catalyst_configuration');
        //$data = $this->findBy(array('prodgroup' => $prodgroup->get('catalyst_product_group_default')));
        $inv = $this->get('catalyst_inventory');
        $data = $inv->findProduct($id)->toData();
        // setup json response
        $resp = new Response(json_encode($data));
        $resp->headers->set('Content-Type', 'application/json');

        return $resp;
    }

    protected function getStockReport()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('select p.code, p.name, p.uom, j.name as j_name from Catalyst\InventoryBundle\Entity\Product p join p.prodgroup j order by p.name asc');              
        return $query->getResult();
    }

    public function deleteImageAction($id, $loop)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CatalystInventoryBundle:Product')->find($id);
        $gallery = $this->getGallery($id);
        $images = $gallery->getImages();
        unlink("uploads/images/".$images[$loop - 1]);
        $em->flush();
        $this->addFlash('success','Image has been deleted');
  
        $params["id"] = $id;    
        return $this->redirect($this->generateUrl('cat_inv_prod_edit_form', $params));
    }

    public function ajaxGetWarehouseStockAction($prod_id, $wh_id)
    {
        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');

        $prod = $em->getRepository('CatalystInventoryBundle:Product')->find($prod_id);

        // product not found
        if ($prod == null)
        {
            $json = [
                'prodtype' => '',
                'uom' => '',
                'current_stock' => 0.00
            ];

            return new JsonResponse($json);
        }

        // TODO: check if warehouse exists
        $wh = $em->getRepository('CatalystInventoryBundle:Warehouse')->find($wh_id);
        $iacc = $wh->getInventoryAccount();

        // get stock count
        $quantity = $inv->getStockCount($iacc, $prod);

        $json = [
            'prodtype' => $prod->getTypeText(),
            'uom' => $prod->getUnitOfMeasure(),
            'current_stock' => $quantity
        ];

        return new JsonResponse($json);
    }
}
