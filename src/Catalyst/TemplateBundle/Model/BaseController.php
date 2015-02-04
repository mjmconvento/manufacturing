<?php

namespace Catalyst\TemplateBundle\Model;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class BaseController extends Controller
{
    protected $title;

    protected function getURL($route_id, $alternate_url)
    {
        try
        {
            return $this->generateUrl($route_id);
        }
        catch (RouteNotFoundException $e)
        {
            return $alternate_url;
        }
    }

    protected function buildBreadcrumbTrail($current)
    {
        $layers = array();
        if ($current == null)
            return $layers;

        $layers[] = array(
            'label' => $current->getLabel(),
            'url' => $this->getURL($current->getID(), null)
        );
        while ($current->getParent() != null)
        {
            $current = $current->getParent();
            $layers[] = array(
                'label' => $current->getLabel(),
                'url' => $this->getURL($current->getID(), null)
            );
        }

        return array_reverse($layers);
    }

    protected function getMenu($selected = null, &$selected_obj)
    {
        // menu
        $menu_handler = $this->get('catalyst_menu');
        $menu_handler->setSelected($selected);

        $selected_obj = $menu_handler->getSelected();

        return $menu_handler->getMenu();
    }

    protected function addFlash($type, $message)
    {
        $this->get('session')->getFlashBag()->add($type, $message);
    }

    protected function getViewParams($subtitle = '', $selected = null)
    {
        $menu = $this->getMenu($selected, $sel_obj);
        $bcrumb = $this->buildBreadcrumbTrail($sel_obj);

        // TODO: generate breadcrumb from menu
        return array(
            'left_menu' => $menu,
            'head_title' => $this->title,
            'head_subtitle' => $subtitle,
            'bcrumb' => $bcrumb,
        );
    }

    protected function checkAccess($acl_id)
    {
        $user = $this->getUser();
        if ($user->hasAccess($acl_id))
            return true;

        throw new AccessDeniedException();
    }

}
