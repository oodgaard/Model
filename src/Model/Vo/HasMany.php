<?php

namespace Model\Vo;
use Model\Entity;

class HasMany extends VoAbstract
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function init()
    {
        return new Entity\Set($this->class);
    }

    public function translate($value)
    {
        return new Entity\Set($this->class, $value);
    }

    public function from($value, $filter = null)
    {
        return new Entity\Set($this->class, $value, $filter);
    }

    public function to($value, $filter = null)
    {
        return $value->to($filter);
    }
}