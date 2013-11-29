<?php

namespace Model\Vo;

class String extends VoAbstract
{
    public function init()
    {
        return '';
    }

    public function translate($value)
    {
        return (string) $value;
    }
}