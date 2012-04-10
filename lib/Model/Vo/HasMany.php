<?php

namespace Model\Vo;
use Model\Entity\Set;

/**
 * One-to-many relationship VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
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
     * @param Set $set The entity set to use.
     * 
     * @return HasMany
     */
    public function __construct(Set $set)
    {
        $this->set = $set;
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->set->init();
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
        $this->set->init()->fill($value);
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
}