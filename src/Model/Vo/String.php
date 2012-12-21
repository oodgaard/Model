<?php

namespace Model\Vo;

class String extends VoAbstract
{
    public function translate($value)
    {
        return (string) $value;
    }
}