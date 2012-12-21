<?php

namespace Model\Vo;
use Model\Entity;

class HasOne extends VoAbstract
{
    private $value;

    public function __construct($class)
    {
        $this->value = new $class;
    }

    public function __clone()
    {
        $this->value = clone $this->value;
    }

    public function set($value)
    {
        $this->get()->clear()->from($value);
    }

    public function get()
    {
        return $this->value;
    }

    public function exists()
    {
        return isset($this->value);
    }

    public function remove()
    {
        $this->value = null;
    }

    public function from($value, $filter = null)
    {
        return $this->get()->from($value, $filter);
    }

    public function to($filter = null)
    {
        return $this->get()->to($filter);
    }
}