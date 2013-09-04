<?php

namespace Model\Vo;
use ReflectionClass;

class VoSet extends VoAbstract
{
    private $class;

    public function __construct($class, array $args = [])
    {
        $this->class = (new ReflectionClass($class))->newInstanceArgs($args);
    }

    public function init()
    {
        return new \ArrayObject();
    }

    public function translate($value)
    {
        $data = new \ArrayObject;

        if (is_array($value) || is_object($value)) {
            foreach ($value as $k => $v) {
                $data->offsetSet($k, $this->class->translate($v));
            }
        }

        return $data;
    }

    public function from($value, $filter = null)
    {
        $data = new \ArrayObject;

        if (is_array($value) || is_object($value)) {
            foreach ($value as $k => $v) {
                $data->offsetSet($k, $this->class->to($v, $filter));
            }
        }

        return $data;
    }

    public function to($value, $filter = null)
    {
        $data = [];

        foreach ($value as $k => $v) {
            $data[$k] = $this->class->to($v, $filter);
        }

        return $data;
    }
}
