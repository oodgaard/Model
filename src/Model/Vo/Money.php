<?php

namespace Model\Vo;

class Money extends Generic
{
    public function translate($value)
    {
        return (float) number_format($value, 2);
    }
}