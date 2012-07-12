<?php

namespace Provider;
use Model\Entity\Entity;

class ContentEntity extends Entity
{
    /**
     * @vo Model\Vo\Integer
     */
    public $id;
    
    /**
     * @vo Model\Vo\String
     */
    public $name;
    
    /**
     * @vo Model\Vo\HasOne new Provider\UserEntity
     */
    public $user;
    
    /**
     * @vo Model\Vo\HasMany new Model\Entity\Set('Provider\CommentEntity')
     */
    public $comments;
}