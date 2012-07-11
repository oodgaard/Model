<?php

namespace Model\Vo;
use Model\Validator\ValidatableInterface;

/**
 * The base value object interface.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
interface VoInterface extends ValidatableInterface
{
    /**
     * Sets the value.
     * 
     * @param mixed $value The value to set.
     * 
     * @return void
     */
    public function set($value);
    
    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get();
    
    /**
     * Returns whether or not the VO has a value.
     * 
     * @return bool
     */
    public function exists();
    
    /**
     * Hook for initializing the VO.
     * 
     * @return void
     */
    public function remove();
}