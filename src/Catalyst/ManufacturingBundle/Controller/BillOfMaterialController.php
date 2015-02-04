<?php

namespace Catalyst\ManufacturingBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\ManufacturingBundle\Entity\BOMInput;
use Catalyst\ManufacturingBundle\Entity\BOMOutput;
use Catalyst\ManufacturingBundle\Entity\BillOfMaterial;
use Catalyst\ValidationException;

class BillOfMaterialController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cat_mfg_bom';
        $this->title = 'Bill of Material';

        $this->list_title = 'Bill of Materials';
        $this->list_type = 'dynamic';
    }

    protected function newBaseClass()
    {
        return new BillOfMaterial();
    }
    
    protected function getObjectLabel($obj)
    {
        if ($obj == null)
            return '';

        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');
        return array(
            $grid->newColumn('Name', 'getName', 'name'),
        );
    }

    protected function padFormParams(&$params, $o = null)
    {
        $inv = $this->get('catalyst_inventory');
        $params['prod_opts'] = $inv->getProductOptions();

        return $params;
    }

    protected function update($o, $data, $is_new = false)
    {
        $em = $this->getDoctrine()->getManager();
        $prod_repo = $em->getRepository('CatalystInventoryBundle:Product');

        $o->setName($data['name']);

        // inputs
        $inputs = $o->getInputs();
        foreach ($inputs as $input)
            $em->remove($input);

        $o->clearInputs();
        if (isset($data['input_id']))
        {
            foreach ($data['input_id'] as $index => $id)
            {
                // get product
                $product = $prod_repo->find($id);
                if ($product == null)
                    continue;

                // initialize
                $qty = $data['input_quantity'][$index];
                $input_obj = new BOMInput();
                $input_obj->setQuantity($qty)
                    ->setProduct($product);

                // add
                $o->addInput($input_obj);
            }
        }

        // outputs
        $outputs = $o->getOutputs();
        foreach ($outputs as $output)
            $em->remove($output);

        $o->clearOutputs();
        if (isset($data['output_id']))
        {
            foreach ($data['output_id'] as $index => $id)
            {
                // get product
                $product = $prod_repo->find($id);
                if ($product == null)
                    continue;

                // initialize
                $qty = $data['output_quantity'][$index];
                $output_obj = new BOMOutput();
                $output_obj->setQuantity($qty)
                    ->setProduct($product);

                // add
                $o->addOutput($output_obj);
            }
        }
    }
}
