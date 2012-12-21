<?php

namespace Model\Vo;

class Float extends Generic
{
    public function translate($value)
    {
        return (float) $value;
    }
}