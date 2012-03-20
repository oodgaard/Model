<?php

namespace Model\Vo;
use Model\Entity\Set;

class HasMany implements VoInterface
{
    /**
     * The entity set.
     * 
     * @var Set
     */
    private $set;
    
    /**
     * Sets up the entity set.
     * 
     * @param $class The class to apply to the set for entities.
     * 
     * @return HasMany
     */
    public function __construct($class = null)
    {
        $this->set = new Set(array(), $class);
    }
    
    /**
     * Fills the set with the value.
     * 
     * @param mixed $value The value to fill the set with.
     * 
     * @return void
     */
    public function set($value)
    {
        $this->set->clean()->fill($value);
    }
    
    /**
     * Returns the set.
     * 
     * @return Set
     */
    public function get()
    {
        return $this->set;
    }
    
    /**
     * Returns whether or not the set exists.
     * 
     * @return bool
     */
    public function exists()
    {
        return !$this->set->isClean();
    }
    
    /**
     * Unsets the set.
     * 
     * @return void
     */
    public function remove()
    {
        $this->set->clean();
    }
}