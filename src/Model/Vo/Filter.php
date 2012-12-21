<?php

namespace Model\Vo;

class Filter extends Generic
{
    private $cb;

    public function __construct(callable $cb)
    {
        $this->cb = $cb;
    }

    public function translate($value)
    {
        $cb = $this->cb;
        return $cb($value);
    }
}