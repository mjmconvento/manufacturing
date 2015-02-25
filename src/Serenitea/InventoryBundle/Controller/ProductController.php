<?php

namespace Serenitea\InventoryBundle\Controller;

use Catalyst\InventoryBundle\Controller\ProductController as Controller;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\CoreBundle\Template\Controller\TrackCreate;
use Catalyst\CoreBundle\Template\Controller\TrackUpdate;
use Symfony\Component\HttpFoundation\Response;
use Catalyst\InventoryBundle\Model\Gallery;
use Catalyst\ValidationException;

class ProductController extends Controller
{
    use TrackUpdate;
    use TrackCreate;

	public function __construct()
	{	
		$this->repo = 'CatalystInventoryBundle:Product';
		parent::__construct();
		$this->route_prefix = 'ser_inv_prod';
		$this->title = 'Supply';

        $this->list_title = 'Supplies';
        $this->list_type = 'dynamic';        
	}

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getName();
    }


	public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'SereniteaInventoryBundle:Product:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
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
            $grid->newColumn('Item Code', 'getSKU', 'sku'),
            $grid->newColumn('Description', 'getName', 'name'),
            $grid->newColumn('Specs', 'getUnitOfMeasure', 'uom'),
            $grid->newColumn('Product Category', 'getName', 'name', 'pg'),
        );
    }

    protected function padFormParams(&$params, $prod = null)
    {
        $em = $this->getDoctrine()->getManager();

        $inv = $this->get('catalyst_inventory');
        $params['pg_opts'] = $inv->getProductGroupOptions();
        $brand_opts = array(0 => '[ Select Brand ]');

        $params['brand_opts'] = $brand_opts + $this->getFieldOptions('Brand');

        $supps = $em->getRepository('CatalystPurchasingBundle:Supplier')->findAll();
        $supp_opts = array();
        foreach ($supps as $supp)
            $supp_opts[$supp->getID()] = $supp->getDisplayName();

        $params['supp_opts'] = $supp_opts;

        // images
        if ($prod->getID())
        {
            $gallery = $this->getGallery($prod->getID());
            $images = $gallery->getImages();
            $params['images'] = $images;
        }

        return $params;
    }

    protected function validate($data, $type)
    {
        // SKU validation check
        // if (empty($data['sku']))
        //     throw new ValidationException('Cannot leave SKU blank');

        // validate name
        if (empty($data['name']))
            throw new ValidationException('Cannot leave name blank');
    }

    protected function update($o, $data, $is_new = false)
    {

        $em = $this->getDoctrine()->getManager();
        $inv = $this->get('catalyst_inventory');

        if($is_new)
        {
            $o->setSKU('');
        }
        
        $o->setName($data['name']);
        $o->setFlagPerishable($data['flag_perishable']);

        // unit of measure
        if (!empty($data['uom']))
            $o->setUnitOfMeasure($data['uom']);
        else
            $o->setUnitOfMeasure('');

        // prices
        $o->setPriceSale($data['price_sale']);
        $o->setPricePurchase($data['price_purchase']);       

        // threshold values
        // $o->setStockMin($data['stock_min']);
        // $o->setStockMax($data['stock_max']);


        // product group
        $pg = $inv->findProductGroup($data['prodgroup_id']);
        if ($pg == null)
            throw new ValidationException('Could not find product group specified.');
        $o->setProductGroup($pg);

        //supplier's code
        $o->setSupplierCode($data['supp_code']);

        $supp = $em->getRepository('CatalystPurchasingBundle:Supplier')->find($data['supplier_id']);
        if ($supp == null)
            throw new ValidationException('Could not find supplier.');
        
        $o->setSupplier($supp);

        $this->updateTrackCreate($o, $data, $is_new);
        $this->updateTrackUpdate($o, $data);
    }

    protected function buildData($o)
    {
        $data = array(
            'id' => $o->getID(),
            'sku' => $o->getSKU(),
            'name' => $o->getName(),
            'uom' => $o->getUnitOfMeasure(),
            // 'flag_service' => $o->isService(),
            // 'flag_sale' => $o->canSell(),
            'supp_code' => $o->getSupplierCode(),
            // 'flag_purchase' => $o->canPurchase(),
            'price_sale' => $o->getPriceSale(),
            'price_purchase' => $o->getPricePurchase(),
            'prodgroup_id' => $o->getProductGroup()->getID(),
            'supplier_id' => $o->getSupplier()->getID(),
            'user_update' => $o->getUserUpdate(),
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
    
    protected function hookPostSave($obj, $is_new = false) {
        $em = $this->getDoctrine()->getManager();
        if($is_new){
            $obj->generateSku();
            $em->persist($obj);
            $em->flush();
        }
    }
}