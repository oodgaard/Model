<?php

namespace Model\Vo;

class Float extends VoAbstract
{
    public function translate($value)
    {
        return (float) $value;
    }
}