<?php

namespace Model\Vo;

/**
 * Works with enumerated values.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Enum extends Generic
{
    /**
     * The allowed values.
     * 
     * @var array
     */
    private $values;
    
    /**
     * Sets up the enumerated VO.
     * 
     * @param array $values The enum values to allow.
     * 
     * @return Enum
     */
    public function __construct(array $values)
    {
        $this->values = $values;
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
        if (in_array($value, $this->values)) {
            parent::set($value);
        }
    }
}