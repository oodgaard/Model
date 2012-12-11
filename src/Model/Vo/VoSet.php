<?php

namespace Model\Vo;
use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;

class VoSet extends VoAbstract
{
    private $arr = [];
    
    private $class;

    private $args;

    public function __construct($class, array $args = [])
    {
        $this->class = new ReflectionClass($class);
        $this->args  = $args;
    }

    public function set($value)
    {
        $this->throwIfNotValid($value);
        
        $this->arr = [];
        
        foreach ($value as $k => $v) {
            $class = $this->instantiate();
            $class->set($v);
            $this->arr[$k] = $v;
        }
    }

    public function get()
    {
        return $this->arr;
    }

    public function exists()
    {
        return count($this->arr) > 0;
    }

    public function remove()
    {
        $this->arr = [];
    }

    private function instantiate()
    {
        $class = $this->class->newInstanceArgs($this->args);
        
        if (!$class instanceof VoInterface) {
            throw new RuntimeException('The VO class specified for the VoSet is not a valid vo.');
        }
        
        return $class;
    }

    private function throwIfNotValid($value)
    {
        if (!is_array($value) || !is_object($value)) {
            throw new InvalidArgumentException(
                'The value must be an object or array. '
                . ucfirst(gettype($value))
                . ' given.'
            );
        }
    }
}