<?php

namespace Provider;
use Model\Entity\Entity;

class ContentEntity extends Entity
{
    /**
     * @var Model\Vo\HasOne new Provider\UserEntity
     */
    private $user;
    
    /**
     * @var Model\Vo\HasMany new Model\Entity\Set('Provider\CommentEntity')
     */
    private $comments;
}