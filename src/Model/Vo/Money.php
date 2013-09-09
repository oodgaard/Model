<?php

namespace Model\Vo;

class Money extends VoAbstract
{
    public function init()
    {
        return 0;
    }

    public function translate($value)
    {
        return (float) number_format($value, 2);
    }
}