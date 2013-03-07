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
        $lower = strtolower($value);

        if ($lower === 'true') {
            return true;
        }

        if ($lower === 'false') {
            return false;
        }

        if ($lower === 'null') {
            return false;
        }

        return (bool) $value;
    }
}