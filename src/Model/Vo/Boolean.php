<?php

namespace Model\Vo;

class Boolean extends VoAbstract
{
    public function translate($value)
    {
        return (bool) $value;
    }
}