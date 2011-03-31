<?php

namespace Provider;
use Habitat\Entity;

class UserEntity extends Entity
{
    public function init()
    {
        $this->hasMany('content', '\Provider\ContentEntity');
    }
}