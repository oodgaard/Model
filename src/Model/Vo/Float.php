<?php

namespace Model\Vo;

class Float extends VoAbstract
{
    public function init()
    {
        return 0;
    }

    public function translate($value)
    {
        return (float) $value;
    }
}