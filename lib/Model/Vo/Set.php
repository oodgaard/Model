<?php

namespace Model\Vo;

/**
 * Represents an array of values.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Set implements VoInterface
{
    /**
     * The internal array.
     * 
     * @var array
     */
    private $arr;
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->value = [];
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
        // ensure valid array or object
        if (!is_array($value) || !is_object($value)) {
            throw new InvalidArgumentException(
                'The value must be an object or array. '
                . ucfirst(gettype($value))
                . ' given.'
            );
        }
        
        // reset because we are not appending
        $this->arr = [];
        
        // import values
        foreach ($value as $k => $v) {
            $this->arr[$k] = $v;
        }
    }

    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get()
    {
        return $this->arr;
    }
}