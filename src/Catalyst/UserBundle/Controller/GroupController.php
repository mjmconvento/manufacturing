<?php

namespace Catalyst\UserBundle\Controller;

use Catalyst\TemplateBundle\Model\CrudController;
use Catalyst\UserBundle\Entity\Group;
use Catalyst\ValidationException;

class GroupController extends CrudController
{
    public function __construct()
    {
        $this->route_prefix = 'cat_user_group';
        $this->title = 'Role';

        $this->list_title = 'Roles';
        $this->list_type = 'static';
    }

    protected function newBaseClass()
    {
        return new Group('');
    }

    protected function getObjectLabel($obj)
    {
        return $obj->getName();
    }

    protected function getGridColumns()
    {
        $grid = $this->get('catalyst_grid');

        return array(
            $grid->newColumn('Name', 'getName', 'name'),
        );
    }

    protected function update($o, $data, $is_new = false)
    {
        // validate name
        if (strlen($data['name']) > 0)
            $o->setName($data['name']);
        else
            throw new ValidationException('Cannot leave name blank');

        $o->clearAccess();
        if (isset($data['acl']))
        {
            foreach ($data['acl'] as $id => $val)
                $o->addAccess($id);
        }
    }

    protected function padFormParams(&$params, $object = null)
    {
        $acl_entries = $this->get('catalyst_acl')->getAllACLEntries();

        $params['acl_entries'] = $acl_entries;

        return $params;
    }

    public function deleteAction($id)
    {
        $this->checkAccess($this->route_prefix . '.delete');

        try
        {
            $this->hookPreAction();
            $em = $this->getDoctrine()->getManager();

            $object = $em->getRepository($this->repo)->find($id);
            $odata = $object->toData();

            if($object->getUserCount() == 0)
            {
                $this->logDelete($odata);
                $em->remove($object);
                $em->flush();

                $this->addFlash('success', $this->title . ' ' . $this->getObjectLabel($object) . ' has been deleted.');

                return $this->redirect($this->generateUrl($this->getRouteGen()->getList())); 
            }
            else
            {
                $this->addFlash('error', 'Could not delete ' . $this->title . ', ' . $object->getUserCount() . ($object->getUserCount() == 1 ? ' user is' : ' users are') . ' using this role.');
                return $this->redirect($this->generateUrl($this->getRouteGen()->getList()));
            }
            
        }
        catch (DBALException $e)
        {
            $this->addFlash('error', 'Could not delete ' . $this->title . '.');
            return $this->redirect($this->generateUrl($this->getRouteGen()->getList()));
        }
    }
}
