<?php

namespace Model\Vo;
use Model\Entity\Entity;
use UnexpectedValueException;

/**
 * One-to-one relationship VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class HasOne implements VoInterface
{
    /**
     * The entity.
     * 
     * @var Entity
     */
    private $entity;
    
    /**
     * Constructs a new relationship VO.
     * 
     * @param Entity $entity The entity to represent each object.
     * 
     * @return HasOne
     */
    public function __construct(Entity $entity)
    {
        $this->entity = new $entity;
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->entity->init();
    }
    
    /**
     * Fills the entity with the specified value.
     * 
     * @param mixed $value The value to fill the entity with.
     * 
     * @return void
     */
    public function set($value)
    {
        $this->entity->init()->fill($value);
    }
    
    /**
     * Returns the entity.
     * 
     * @return Entity
     */
    public function get()
    {
        return $this->entity;
    }
}