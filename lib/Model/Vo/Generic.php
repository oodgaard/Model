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
class Generic implements VoInterface
{
    /**
     * The generic value.
     * 
     * @var mixed
     */
    private $value;
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->set(null);
    }
    
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
}