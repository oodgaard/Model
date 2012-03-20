<?php

namespace Model\Entity;
use Model\Behavior\BehaviorInterface;
use Model\Filter\FilterInterface;
use Model\Validator\ValidatorInterface;
use Model\Validator\ValidatorException;
use Model\Vo\VoInterface;
use ReflectionClass;

/**
 * The main entity class. All model entities should derive from this class.
 * 
 * @category Entities
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Entity implements AccessibleInterface
{
    /**
     * The data in the entity.
     * 
     * @var array
     */
    private $data = array();
    
    /**
     * The filters applied to the object.
     * 
     * @var array
     */
    private $filters = array();
    
    /**
     * The validators applied to the entity.
     * 
     * @var array
     */
    private $validators = array();
    
    /**
     * Constructs a new entity and sets any passed values.
     * 
     * @param mixed $vals The values to set.
     * 
     * @return Entity
     */
    public function __construct($values = array())
    {
        $this->init();
        $this->fill($values);
    }

    /**
     * Allows the entity to be set up before any data is imported.
     * 
     * @return void
     */
    public function init()
    {

    }
    
    /**
     * Easy property setting.
     * 
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->data[$name]) && $this->filter($name)) {
            $this->data[$name]->set($value);
        }
    }
    
    /**
     * For easy property getting.
     * 
     * @param string $name The property name.
     * 
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name]->get();
        }
    }
    
    /**
     * For easy property checking.
     * 
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]) && $this->data[$name]->exists();
    }
    
    /**
     * For easy property unsetting.
     * 
     * @param string $name The value to unset.
     * 
     * @return Entity
     */
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            $this->data[$name]->remove();
        }
    }
    
    /**
     * Cleans the entity by clearing each VO.
     * 
     * @return Entity
     */
    public function clean()
    {
        foreach ($this->data as $vo) {
            $vo->remove();
        }
        return $this;
    }
    
    /**
     * Returns whether or not the entity is clean.
     * 
     * @return bool
     */
    public function isClean()
    {
        foreach ($this->data as $item) {
            if ($item->exists()) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Applies value objects to the entity.
     * 
     * @param string      $name The property name.
     * @param VoInterface $vo   The Vo to use.
     * 
     * @return Entity
     */
    public function setVo($name, VoInterface $vo)
    {
        $this->data[$name] = $vo;
        return $this;
    }
    
    /**
     * Returns the specified VO.
     * 
     * @param string $name The name of the VO.
     * 
     * @throws RuntimeException If the VO does not exist on the entity.
     * 
     * @return Vo
     */
    public function getVo($name)
    {
        if (!isset($this->data[$name])) {
            throw new RuntimeException('The VO "' . $name . '" does not exist on "' . get_class($this) . '".');
        }
        return $this->data[$name];
    }
    
    /**
     * Adds a filter to the entity.
     * 
     * @param FilterInterface $filter The filter to use.
     * 
     * @return Entity
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }
    
    /**
     * Adds a validator to the entity.
     * 
     * @param ValidatorInterface $validator The validator to use.
     * 
     * @return Entity
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }
    
    /**
     * Applies the behavior to the entity.
     * 
     * @param BehaviorInterface $behavior The behavior to apply.
     * 
     * @return Entity 
     */
    public function actAs(BehaviorInterface $behavior)
    {
        $behavior->behave($this);
        return $this;
    }
    
    /**
     * Validates the entity and throws an exception if it is not valid.
     * 
     * @throws Exception If the entity is not valid.
     * 
     * @return Entity
     */
    public function validate()
    {
        // validate the entity against all validators
        foreach ($this->validators as $validator) {
            if ($validator->validate($this) === false) {
                throw new ValidatorException(
                    'The entity "'
                    . get_class($this)
                    . '" did not pass "'
                    . get_class($validator)
                    . '" validation.'
                );
            }
        }
        
        // validate each relationship
        foreach ($this->data as $item) {
            $item = $item->get();
            if ($item instanceof ValidatableInterface) {
                $item->validate();
            }
        }
        
        return $this;
    }
    
    /**
     * Fills the entity with the specified values.
     * 
     * @param mixed $traversable The traversable to import.
     * 
     * @return Entity
     */
    public function fill($traversable)
    {
        if (is_array($traversable) || is_object($traversable)) {
            foreach ($traversable as $k => $v) {
                $this->__set($k, $v);
            }
        }
        return $this;
    }
    
    /**
     * Converts the entity to an array.
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->data as $k => $v) {
            if ($v->exists()) {
                $array[$k] = $v->get();
            }
        }
        return $array;
    }
    
    /**
     * For setting properties like an array.
     * 
     * @param string $name  The property to set.
     * @param mixed  $value The value to set.
     * 
     * @return Entity
     */
    public function offsetSet($name, $value)
    {
        return $this->__set($name, $value);
    }
    
    /**
     * For getting properties like an array.
     * 
     * @param string $name The property to get.
     * 
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->__get($name);
    }
    
    /**
     * For isset checking using array syntax.
     * 
     * @param string $name The property to check.
     * 
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }
    
    /**
     * For unsetting using array syntax.
     * 
     * @param string $name The property to unset.
     * 
     * @return Entity
     */
    public function offsetUnset($name)
    {
        return $this->__unset($name);
    }
    
    /**
     * Returns the current item in the iteration.
     * 
     * @return mixed
     */
    public function current()
    {
        return $this->__get($this->key());
    }
    
    /**
     * Returns the current key in the iteration.
     * 
     * @return string
     */
    public function key()
    {
        return key($this->data);
    }
    
    /**
     * Moves to the next item in the iteration.
     * 
     * @return Entity
     */
    public function next()
    {
        next($this->data);
        return $this;
    }
    
    /**
     * Resets the iteration.
     * 
     * @return Entity
     */
    public function rewind()
    {
        reset($this->data);
        return $this;
    }
    
    /**
     * Returns whether or not to keep iteration.
     * 
     * @return bool
     */
    public function valid()
    {
        return !is_null($this->key());
    }
    
    /**
     * Counts the number of values in the entity.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
    
    /**
     * Serializes the data and returns it.
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }
    
    /**
     * Unserializes and sets the specified data.
     * 
     * @param string The serialized string to unserialize and set.
     * 
     * @return void
     */
    public function unserialize($data)
    {
        $this->fill(unserialize($data));
    }
    
    /**
     * Returns true if the specified property can be set or false if not.
     * 
     * @param string $name The property name.
     * 
     * @return bool
     */
    private function filter($name)
    {
        foreach ($this->filters as $filter) {
            if ($filter->filter($name) === false) {
                return false;
            }
        }
        return true;
    }
}
