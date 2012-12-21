<?php

namespace Model\Vo;

class UniqueId extends VoAbstract
{
    public function init()
    {
        return md5(mt_rand() . microtime() . mt_rand());
    }

    public function translate($value)
    {
        return $value;
    }
}