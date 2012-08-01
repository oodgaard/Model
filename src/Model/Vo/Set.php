<?php

namespace Model\Vo;
use InvalidArgumentException;

/**
 * Represents an array of values.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Set extends VoAbstract
{
    /**
     * The internal array.
     * 
     * @var array
     */
    private $value = [];
    
    /**
     * Sets the value.
     * 
     * @param array $value The value to set.
     * 
     * @return void
     */
    public function set($value)
    {
        // ensure valid array or object
        if (!is_array($value)) {
            return;
        }
        
        // reset because we are not appending
        $this->value = [];
        
        // import values
        foreach ($value as $k => $v) {
            $this->value[$k] = $v;
        }
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
        return count($this->value) > 0;
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function remove()
    {
        $this->value = [];
    }
}