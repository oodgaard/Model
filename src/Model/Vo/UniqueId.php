<?php

namespace Model\Vo;

class UniqueId extends Generic
{
    public function __construct()
    {
        parent::set(md5(mt_rand() . microtime() . mt_rand()));
    }
}