<?php

namespace Model\Vo;

class Integer extends VoAbstract
{
    public function init()
    {
        return 0;
    }

    public function translate($value)
    {
        return (int) $value;
    }
}