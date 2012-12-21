<?php

namespace Model\Vo;

class Integer extends Generic
{
    public function translate($value)
    {
        return (int) $value;
    }
}