<?php

namespace Model\Vo;

class Boolean extends VoAbstract
{
    public function init()
    {
        return false;
    }

    public function translate($value)
    {
        return (bool) $value;
    }
}