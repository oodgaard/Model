<?php

namespace Model\Vo;
use Model\Entity;

/**
 * One-to-one relationship VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class HasOne extends VoAbstract
{
    /**
     * The value.
     * 
     * @var mixed
     */
    private $value;

    /**
     * Sets up the entity.
     * 
     * @param string $class The entity class name to use.
     * 
     * @return HasMany
     */
    public function __construct($class)
    {
        $this->value = new $class;
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
        $this->value->clear()->fill($value);
    }

    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }
    
    /**
     * Returns whether or not the VO has a value.
     * 
     * @return bool
     */
    public function exists()
    {
        return true;
    }
    
    /**
     * Hook for initializing the VO.
     * 
     * @return void
     */
    public function remove()
    {
        $this->value->clear();
    }
}