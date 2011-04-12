<?php

namespace Provider;
use Model\Entity;

class UserEntity extends Entity
{
    public function init()
    {
        $this->hasMany('content', '\Provider\ContentEntity');
    }
}