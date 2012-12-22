<?php

namespace Model\Vo;

class Integer extends VoAbstract
{
    public function translate($value)
    {
        return (int) $value;
    }
}