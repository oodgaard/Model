<?php

namespace Model\Vo;
use Model\Entity\Set as EntitySet;

/**
 * One-to-many relationship VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class HasMany extends VoAbstract
{
    /**
     * The class name.
     * 
     * @var string
     */
    private $class;

    /**
     * The value.
     * 
     * @var mixed
     */
    private $value;

    /**
     * Sets up the entity.
     * 
     * @param string $class The entity class name to use.
     * 
     * @return HasMany
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Fills the set with the value.
     * 
     * @param mixed $value The value to fill the set with.
     * 
     * @return void
     */
    public function set($value)
    {
        $this->get()->clear()->fill($value);
    }

    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get()
    {
        if (!$this->exists()) {
            $this->value = new EntitySet($this->class);
        }
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
     * Hook for initializing the VO.
     * 
     * @return void
     */
    public function remove()
    {
        $this->value->clear();
    }
}