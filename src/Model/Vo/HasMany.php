<?php

namespace Model\Vo;
use Model\Entity\Set as EntitySet;

class HasMany extends VoAbstract
{
    private $class;

    private $value;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function set($value)
    {
        $this->get()->clear()->from($value);
    }

    public function get()
    {
        if (!$this->exists()) {
            $this->value = new EntitySet($this->class);
        }

        return $this->value;
    }

    public function exists()
    {
        return isset($this->value);
    }

    public function remove()
    {
        $this->value->clear();
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