<?php

namespace Model\Vo;
use Closure;
use InvalidArgumentException;

/**
 * Loads a value using the proxy callback if it hasn't been loaded yet.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Proxy extends Generic
{
    /**
     * The callback to call.
     * 
     * @var Closure
     */
    private $cb;
    
    /**
     * Whether or not it has been loaded.
     * 
     * @var bool
     */
    private $loaded = false;
    
    /**
     * Sets the value.
     * 
     * @param mixed $cb   The callback to use.
     * @param array $args The arguments to pass to the callback.
     * 
     * @return void
     */
    public function __construct($cb)
    {
        if (!is_callable($cb)) {
            throw new InvalidArgumentException('The specifie callback is not valid.');
        }
        $this->cb = $cb;
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->loaded = false;
        parent::set(null);
    }
    
    /**
     * Sets the value and marks the proxy as loaded.
     * 
     * @param mixed $value The value to set
     * 
     * @return void
     */
    public function set($value)
    {
        $this->loaded = true;
        parent::set($value);
    }
    
    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get()
    {
        if (!$this->loaded) {
            $this->loaded = true;
            parent::set(call_user_func($this->cb));
        }
        return parent::get();
    }
}