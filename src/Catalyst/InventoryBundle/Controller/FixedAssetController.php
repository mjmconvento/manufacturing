<?php

namespace Catalyst\InventoryBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\InventoryBundle\Entity\Product;
use Catalyst\InventoryBundle\Entity\ServiceTask;
use Catalyst\ValidationException;
use Catalyst\InventoryBundle\Model\Gallery;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

class FixedAssetController extends CrudController
{

    public function __construct()
    {
        $this->route_prefix = 'feac_inv_fixed_asset';
        $this->title = 'Fixed Assets';

        $this->list_title = 'Fixed Assets';
        $this->list_type = 'dynamic';
        $this->repo = 'CatalystInventoryBundle:Product';
    }

    public function indexAction()
    {
        $this->hookPreAction();

        $gl = $this->setupGridLoader();

        $params = $this->getViewParams('List');

        $twig_file = 'FareastInventoryBundle:FixedAsset:index.html.twig';

        $params['list_title'] = $this->list_title;
        $params['grid_cols'] = $gl->getColumns();

        return $this->render($twig_file, $params);
    }

    protected function setupGridLoader()
    {
        $grid = $this->get('catalyst_grid');

        $loader = parent::setupGridLoader();

        $fg = $grid->newFilterGroup();
        $fg->where('o.type_id = :type_id')
            ->setParameter('type_id', Product::TYPE_FIXED_ASSET);

        $loader->setQBFilterGroup($fg);

        return $loader;
    }


    // TODO : Find delete error
    public function addSubmitAction()
    {
        $this->checkAccess($this->route_prefix . '.add');

        $this->hookPreAction();
        $obj = $this->add();
        $obj->setUserCreate($this->getUser());
        try
        {
            $this->persist($obj);

            $this->addFlash('success', $this->title . ' added successfully.');

            return $this->redirect($this->generateUrl($this->getRouteGen()->getList()));
        }
        catch (ValidationException $e)
        {
            $this->addFlash('error', $e->getMessage());
            return $this->addError($obj);
        }
        catch (DBALException $e)
        {
            print_r($e->getMessage());
            $this->addFlash('error', 'Database error encountered. Possible duplicate.');
            error_log($e->getMessage());
            return $this->addError($obj);
        }
    }

    protected function newBaseClass()
    {
        return new Product();
    }

    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';
        return $obj->getSKU();
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
        $params['brand_opts'] = $this->getFieldOptions('Brand');

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

        // unit of measure
        if (!empty($data['uom']))
            $o->setUnitOfMeasure($data['uom']);
        else
            $o->setUnitOfMeasure('');


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

        $o->setTypeID(6);


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
}
