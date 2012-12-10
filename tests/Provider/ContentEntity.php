<?php

namespace Provider;
use Model\Entity\Entity;

/**
 * @mapper testMapper Provider\ContentMapper
 * 
 * @validator Provider\ContentValidator Test error message.
 * @validator contentValidator          Test error message.
 */
class ContentEntity extends Entity
{
    /**
     * @var Model\Vo\Integer
     */
    public $id;
    
    /**
     * @var Model\Vo\String
     * 
     * @validator validateNameExists Testing :id.
     */
    public $name;
    
    /**
     * @var Model\Vo\HasOne 'Provider\UserEntity'
     */
    public $user;
    
    /**
     * @var Model\Vo\HasMany 'Provider\CommentEntity'
     */
    public $comments;

    public $validatedUsingClass = false;

    public $validatedUsingMethod = false;

    public function contentValidator(self $content)
    {
        $this->validatedUsingMethod = true;
    }

    public function validateNameExists($name)
    {
        return $name ?: false;
    }
}