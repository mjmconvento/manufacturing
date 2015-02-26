<?php

namespace Catalyst\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\GroupInterface;
use Catalyst\InventoryBundle\Template\Entity\HasWarehouse;
use stdClass;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_user")
 */
class User extends BaseUser
{
    use HasWarehouse;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="user_usergroup")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $groups;



    /** @ORM\Column(type="string", length=50, nullable=true) */
    protected $name;

    protected $acl_cache;

    public function __construct()
    {
        parent::__construct();
        $this->roles = array();
        $this->groups = new ArrayCollection();
        $this->acl_cache = array();
    }


    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    
    public function addGroup(GroupInterface $role)
    {
        $this->groups->add($role);
        return $this;
    }

    public function clearGroups()
    {
        $this->groups->clear();
    }

    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getGroups()
    {
        return $this->groups;
    }


    public function getGroupsText()
    {
        $groups = array();
        foreach ($this->groups as $g)
            $groups[] = $g->getName();
        return implode(', ', $groups);
    }

    public function getLastLoginText()
    {
        if ($this->getLastLogin() == null)
            return 'Never';
        return $this->getLastLogin()->format('M d, Y - H:m:s');
    }

    public function getEnabledText()
    {
        if ($this->enabled)
            return 'Enabled';
        return 'Disabled';
    }

    public function hasAccess($acl_key)
    {
        // DEBUG: allow all for admin user
        if ($this->getUsername() == 'admin')
            return true;

        // check acl cache
        if (isset($this->acl_cache[$acl_key]))
            return $this->acl_cache[$acl_key];

        // go through all groups and check
        foreach ($this->groups as $group)
        {
            if ($group->hasAccess($acl_key))
            {
                $this->acl_cache[$acl_key] = true;
                return true;
            }
        }

        // no access
        $this->acl_cache[$acl_key] = false;
        return false;
    }

    public function toData()
    {
        $groups = array();
        foreach ($this->groups as $group)
            $groups[] = $group->toData(false);

        $data = new stdClass();
        $data->id = $this->id;
        $data->username = $this->username;
        $data->email = $this->email;
        $data->enabled = $this->enabled;
        $data->groups = $groups;

        return $data;
    }
}
