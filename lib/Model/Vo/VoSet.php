<?php

namespace Model\Vo;
use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;

/**
 * An array of VOs.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class VoSet implements VoInterface
{
    /**
     * The array holding the VOs.
     * 
     * @var array
     */
    private $arr;
    
    /**
     * The class to use.
     * 
     * @var string
     */
    private $class;
    
    /**
     * The arguments to use.
     * 
     * @var array
     */
    private $args;
    
    /**
     * Sets up the VO array.
     * 
     * @param string $class The VO class to use.
     * @param array  $args  The array of arguments to pass to the VO.
     * 
     * @return VoSet
     */
    public function __construct($class, array $args = array())
    {
        $this->class = new ReflectionClass($class);
        $this->args  = $args;
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->arr = [];
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
        
        foreach ($value as $k => $v) {
            $class = $this->instantiate();
            $class->set($v);
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
    
    /**
     * Instantiates the VO using the class and arguments provided to the constructor.
     * 
     * @throws RuntimeException If the class is not a valid VO.
     * 
     * @return VoInterface
     */
    private function instantiate()
    {
        $class = $this->class->newInstanceArgs($this->args);
        
        // ensure vo interface
        if (!$class instanceof VoInterface) {
            throw new RuntimeException('The VO class specified for the VoSet is not a valid vo.');
        }
        
        return $class;
    }
}