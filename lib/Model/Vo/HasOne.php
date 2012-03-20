<?php

namespace Model\Vo;
use Model\Entity\Entity;
use UnexpectedValueException;

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
     * @param string $class The class to represent each object.
     * 
     * @return HasOne
     */
    public function __construct($class)
    {
        $this->entity = new $class
        if (!$this->entity instanceof Entity) {
            throw new UnexpectedValueException('The has-one "' . $class . '" is not a valid entity.');
        }
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
        $this->entity->clean()->fill($value);
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
    
    /**
     * Returns whether or not the set exists.
     * 
     * @return bool
     */
    public function exists()
    {
        return !$this->entity->isClean();
    }
    
    /**
     * Unsets the set.
     * 
     * @return void
     */
    public function remove()
    {
        $this->entity->clean();
    }
}