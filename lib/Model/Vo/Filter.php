<?php

namespace Model\Vo;
use InvalidArgumentException;

/**
 * Filter VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Filter extends Generic
{
    /**
     * The callback filter.
     * 
     * @var Closure
     */
    private $cb;
    
    /**
     * Sets up the filter.
     * 
     * @param Closure $cb The filter callback.
     * 
     * @return Filter
     */
    public function __construct($cb)
    {
        if (!is_callable($cb)) {
            throw new InvalidArgumentException('The filter callback must be callable.');
        }
        $this->cb = $cb;
    }
    
    /**
     * Sets the default value.
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
        parent::set(call_user_func($this->cb, $value));
    }
}