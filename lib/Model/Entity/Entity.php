<?php

namespace Model\Entity;
use Model\Behavior\BehaviorInterface;
use Model\Configurator;
use Model\Mapper\MapperInterface;
use Model\Repository;
use Model\Validator\Assertable;
use Model\Validator\AssertableInterface;
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
class Entity implements AccessibleInterface, AssertableInterface
{
    use Assertable;
    
    /**
     * Autoloaders to use for autoloading data onto VOs.
     * 
     * @var array
     */
    private $autoloaders = [];
    
    /**
     * The data in the entity.
     * 
     * @var array
     */
    private $data = [];
    
    /**
     * Mappers assigned to the entity.
     * 
     * @var array
     */
    private $mappers = [];
    
    /**
     * Constructs, configures and fills the entity with data, if any is passed.
     * 
     * @param mixed  $data   The data to fill the entity with.
     * @param string $mapper The mapper to use to import the data.
     * 
     * @return Entity
     */
    public function __construct($data = [], $mapper = null)
    {
        $this->init();
        $this->fill($data, $mapper);
    }
    
    /**
     * Applies the specified value to the VO with the specified name.
     * 
     * @param string $name  The VO name.
     * @param mixed  $value The value to set.
     * 
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->data[$name])) {
            $this->data[$name]->set($value);
        }
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
            if (isset($this->autoloaders[$name]) && !$this->data[$name]->exists()) {
                $this->data[$name]->set($this->{$this->autoloaders[$name]}());
            }
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
        return isset($this->data[$name]) && $this->data[$name]->exists();
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
            $this->data[$name]->remove();
        }
    }
    
    /**
     * Configuration hook for setting up the entity.
     * 
     * @return void
     */
    public function init()
    {
        $conf = new Configurator\DocComment;
        $conf->configure($this);
    }
    
    /**
     * Clears all data on the entity.
     * 
     * @return Entity
     */
    public function clear()
    {
        foreach ($this->data as $vo) {
            $vo->remove();
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
        $this->data[$name] = $vo;
        return $this;
    }
    
    /**
     * Returns the specified VO.
     * 
     * @param string $name The VO name.
     * 
     * @return VoInterface
     */
    public function getVo($name)
    {
        if (!isset($this->data[$name])) {
            throw new RuntimeException(sprintf('The VO "%s" does not exist.', $name));
        }
        return $this->data[$name];
    }
    
    /**
     * Returns whether or not the specified VO exists.
     * 
     * @param string $name The VO name.
     * 
     * @return bool
     */
    public function hasVo($name)
    {
        return isset($this->data[$name]);
    }
    
    /**
     * Removes the specified VO if it exists.
     * 
     * @param string $name The VO name.
     * 
     * @return Entity
     */
    public function removeVo($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
    }
    
    /**
     * Sets the entity to use for exporting data.
     * 
     * @param string          $name   The exporter name.
     * @param MapperInterface $mapper The mapper class name.
     * 
     * @return Entity
     */
    public function setMapper($name, MapperInterface $mapper)
    {
        $this->mappers[$name] = $mapper;
        return $this;
    }
    
    /**
     * Returns the specified mapper.
     * 
     * @param string $name The mapper name.
     * 
     * @return MapperInterface
     */
    public function getMapper($name)
    {
        if (!isset($this->mappers[$name])) {
            throw new RuntimeException(sprintf('The mapper "%s" does not exist.', $name));
        }
        return $this->mappers[$name];
    }
    
    /**
     * Returns whether or not the specified mapper exists.
     * 
     * @param string $name The mapper name.
     * 
     * @return bool
     */
    public function hasMapper($name)
    {
        return isset($this->mappers[$name]);
    }
    
    /**
     * Removes the specified mapper if it exists.
     * 
     * @param string $name The mapper name.
     * 
     * @return Entity
     */
    public function removeMapper($name)
    {
        if (isset($this->mappers[$name])) {
            unset($this->mappers[$name]);
        }
        return $this;
    }
    
    /**
     * Applies the autoloader to the specified VO.
     * 
     * @param string $name   The VO name.
     * @param string $method The method to use.
     * 
     * @return Entity
     */
    public function setAutoloader($name, $method)
    {
        if (!method_exists($this, $method)) {
            throw new RuntimeException(sprintf(
                'The autoload method "%s" does not exist on "%s".',
                $method,
                get_class($this)
            ));
        }
        
        $this->autoloaders[$name] = $method;
        
        return $this;
    }
    
    /**
     * Returns the specified autoloader.
     * 
     * @param string $name The autoloader name.
     * 
     * @return string
     */
    public function getAutoloader($name)
    {
        if (!isset($this->autoloaders[$name])) {
            throw new RuntimeException(sprintf('The autoloader "%s" does not exist.', $name));
        }
        return $this->autoloaders[$name];
    }
    
    /**
     * Returns whether or not the specified autoloader exists.
     * 
     * @param string $name The autoloader name.
     * 
     * @return bool
     */
    public function hasAutoloader($name)
    {
        return isset($this->autoloaders[$name]);
    }
    
    /**
     * Removes the specified autoloader if it exists.
     * 
     * @param string $name The autoloader name.
     * 
     * @return Entity
     */
    public function removeAutoloader($name)
    {
        if (isset($this->autoloaders[$name])) {
            unset($this->autoloaders[$name]);
        }
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
        // if there is no data don't do anything
        if (!$data) {
            return $this;
        }
        
        // if there is a mapper we must work some magic
        if ($mapper && isset($this->mappers[$mapper])) {
            $data = $this->makeDataArrayFromAnything($data);
            $data = $this->mappers[$mapper]->map($data);
        }
        
        // let the VOs worry about the value
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $current = $this->__get($k);
                if ($current instanceof AccessibleInterface) {
                    $current->fill($v, $mapper);
                } else {
                    $this->__set($k, $v);
                }
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
            $v = $this->__get($k);
            
            if ($v instanceof AccessibleInterface) {
                $v = $v->toArray($mapper);
            }
            
            $array[$k] = $v;
        }
        
        if ($mapper && isset($this->mappers[$mapper])) {
            $array = $this->mappers[$mapper]->map($array);
        }
        
        return $array;
    }
    
    /**
     * Validates the entity and returns the error messages.
     * 
     * @return array
     */
    public function validate()
    {
        $messages = [];
        
        // validate each VO
        foreach ($this->data as $vo) {
            if ($voMessages = $vo->validate()) {
                $messages = array_merge($messages, $voMessages);
            }
        }
        
        // then validate the entity
        foreach ($this->validators as $message => $validator) {
            if (call_user_func($validator, $this) === false) {
                $messages[] = $message;
            }
        }
        
        // format error messages
        foreach ($messages as &$message) {
            foreach ($this->data as $name => $vo) {
                if (is_scalar($vo = $vo->get())) {
                    $message = str_replace(':' . $name, $vo, $message);
                }
            }
        }
        
        return $messages;
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
        return $this->__isset($name);
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
        $this->__unset($name);
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
     * Returns the current key of the current item in the iteration.
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
    
    /**
     * Makes an array from an array or object.
     * 
     * @param mixed $data The data to turn into an array.
     * 
     * @return array
     */
    private function makeDataArrayFromAnything($data)
    {
        // arrays get passed through
        if (is_array($data)) {
            return $data;
        }
        
        // objects get a shallow conversion because we do
        // not need a deep conversion and it is faster
        if (is_object($data)) {
            $arr = [];
            foreach ($data as $k => $v) {
                $arr[$k] = $v;
            }
            return $arr;
        }
        
        // if it is anything else, just return an empty array
        return [];
    }
}