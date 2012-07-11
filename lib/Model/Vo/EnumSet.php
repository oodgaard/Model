<?php

namespace Model\Vo;

/**
 * Works with a set of enumerated values.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class EnumSet extends Generic
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
        if (!is_array($value)) {
            return;
        }
        
        // values to add to the set
        $add = [];
        
        // add all values
        foreach ($value as $k => $v) {
            if (in_array($v, $this->values)) {
                $add[$k] = $v;
            }
        }
        
        // overwrite enum list
        parent::set($add);
    }
}