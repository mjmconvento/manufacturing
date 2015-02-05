<?php

namespace Catalyst\GridBundle\Model\Grid;

class JoinRepo
{
    protected $alias;
    protected $field;
    protected $getter;

    public function __construct($alias, $field, $getter)
    {
        $this->alias = $alias;
        $this->field = $field;
        $this->getter = $getter;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getGetter()
    {
        return $this->getter;
    }
}
