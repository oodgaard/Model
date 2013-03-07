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
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        return (bool) $value;
    }
}