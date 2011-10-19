<?php

namespace Provider;
use Model\Entity\EntityAbstract;

class ContentEntity extends EntityAbstract
{
    public $init = false;
    
    public function init()
    {
        $this->hasOne('user', '\Provider\UserEntity');
        $this->hasMany('comments', '\Provider\CommentEntity');
        $this->init = true;
    }
}