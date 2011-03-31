<?php

namespace Provider;
use Habitat\Entity;

class ContentEntity extends Entity
{
    public $init = false;
    
    public function init()
    {
        $this->hasOne('user', '\Provider\UserEntity');
        $this->hasMany('comments', '\Provider\CommentEntity');
        $this->init = true;
    }
}