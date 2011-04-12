<?php

namespace Provider;
use Model\Entity;

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