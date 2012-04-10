<?php

namespace Model\Entity;
use Model\Behavior\BehaviorInterface;
use Model\Configurator;
use Model\Mapper\MapperInterface;
use Model\Validator\ValidatorInterface;
use Model\Validator\ValidatorException;
use Model\Vo\Generic;
use Model\Vo\VoInterface;
use RuntimeException;

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
     * Mappers assigned to the entity.
     * 
     * @var array
     */
    private $mappers = [];
    
    /**
     * The validators applied to the entity.
     * 
     * @var array
     */
    private $validators = array();
    
    /**
     * Constructs, configures and fills the entity with data, if any is passed.
     * 
     * @param mixed  $data   The data to fill the entity with.
     * @param string $mapper The mapper to use to import the data.
     * 
     * @return Entity
     */
    public function __construct($data = array(), $mapper = null)
    {
        $this->configure();
        $this->fill($data, $mapper);
    }
    
    /**
     * Applies the specified value to the VO with the specified name.
     * 
     * @param string $name  The VO name.
     * @param mixed  $value The value to set.
     * 
     * @return Entity
     */
    public function __set($name, $value)
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = new Generic;
        }
        $this->data[$name]->set($value);
    }
    
    /**
     * Returns the value of the specified VO.
     * 
     * @param string $name The VO name.
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
     * Returns whether or not the VO exists.
     * 
     * @param string $name The name of the VO.
     * 
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    
    /**
     * Removes the VO from the object.
     * 
     * @param string $name The name of the VO.
     * 
     * @return void
     */
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }
    
    /**
     * Configuration hook for setting up the entity.
     * 
     * @return void
     */
    public function configure()
    {
        $conf = new Configurator\DocComment;
        $conf->configure($this);
    }
    
    /**
     * Initializes each VO.
     * 
     * @return Entity
     */
    public function init()
    {    
        foreach ($this->data as $vo) {
            $vo->init();
        }
        return $this;
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
        $vo->init();
        $this->data[$name] = $vo;
        return $this;
    }
    
    /**
     * Sets the entity to use for exporting data.
     * 
     * @param string          $name   The exporter name.
     * @param MapperInterface $mapper The mapper used to export the data.
     * 
     * @return Entity
     */
    public function setMapper($name, MapperInterface $mapper)
    {
        $this->mappers[$name] = $mapper;
        return $this;
    }
    
    /**
     * Fills the entity with the specified data.
     * 
     * @param mixed  $data   The data to fill the entity with.
     * @param string $mapper The mapper to use to import the data.
     * 
     * @return Entity
     */
    public function fill($data, $mapper = null)
    {
        if (is_array($data) || is_object($data)) {
            if (isset($this->mappers[$mapper])) {
                $data = $this->mappers[$mapper]->map($data);
            }
            
            foreach ($data as $k => $v) {
                $this->__set($k, $v);
            }
        }
        
        return $this;
    }
    
    /**
     * Converts the entity to an array.
     * 
     * @param string $mapper The mapper to use to export the data.
     * 
     * @return array
     */
    public function toArray($mapper = null)
    {
        $array = array();
        
        foreach ($this->data as $k => $v) {
            $v = $v->get();
            if ($v instanceof AccessibleInterface) {
                $v = $v->toArray();
            }
            $array[$k] = $v;
        }
        
        if (isset($this->mappers[$mapper])) {
            $array = $this->mappers[$mapper]->map($array);
        }
        
        return $array;
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
            if ($item instanceof AccessibleInterface) {
                $item->validate();
            }
        }
        
        return $this;
    }
    
    /**
     * Sets the VO value.
     * 
     * @param string $name  The name of the VO.
     * @param mixed  $value The value of the VO.
     * 
     * @return void
     */
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }
    
    /**
     * Returns the value of the VO.
     * 
     * @param string $name The name of the VO.
     * 
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->__get($name);
    }
    
    /**
     * Returns whether or not the VO exists.
     * 
     * @param string $name The name of the VO.
     * 
     * @return bool
     */
    public function offsetExists($name)
    {
        
    }
    
    /**
     * Removes the VO from the object.
     * 
     * @param string $name The name of the VO.
     * 
     * @return void
     */
    public function offsetUnset($name)
    {
        
    }
    
    /**
     * Returns the number of VOs on the object.
     * 
     * @return int
     */
    public function count()
    {
       return count($this->data); 
    }
    
    /**
     * Returns the current item in the iteration.
     * 
     * @return mixed
     */
    public function current()
    {
        return current($this->data)->get();
    }
    
    /**
     * Returns the current key of the current iten in the iteration.
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
     * @return void
     */
    public function next()
    {
        next($this->data);
    }
    
    /**
     * Resets iteration.
     * 
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
    }
    
    /**
     * Returns whether or not the iteration is still valid.
     * 
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
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
}