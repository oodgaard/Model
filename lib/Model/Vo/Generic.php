<?php

namespace Model\Vo;

/**
 * Works with any value.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Generic extends VoAbstract
{
    /**
     * The generic value.
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * Sets the value.
     * 
     * @param mixed $value The value to set.
     * 
     * @return void
     */
    public function set($value)
    {
        $this->value = $value;
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
        return isset($this->value);
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function remove()
    {
        $this->value = null;
    }
}