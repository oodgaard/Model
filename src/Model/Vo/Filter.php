<?php

namespace Model\Vo;
use InvalidArgumentException;

class Filter extends Generic
{
    private $cb;

    public function __construct(callable $cb)
    {
        $this->cb = $cb;
    }

    public function set($value)
    {
        $cb = $this->cb;
        parent::set($cb($value));
    }
}