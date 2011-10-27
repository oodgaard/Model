<?php

namespace Model\Entity;

/**
 * The main entity class. All model entities should derive from this class.
 * 
 * @category Entities
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class EntityAbstract implements AccessibleInterface
{
    /**
     * The data in the entity.
     * 
     * @var array
     */
    private $data = array();
    
    /**
     * The whitelisted properties.
     * 
     * @var array
     */
    private $whitelist = array();
    
    /**
     * The blacklisted properties.
     * 
     * @var array
     */
    private $blacklist = array();
    
    /**
     * One-to-one entity relationships.
     * 
     * @var array
     */
    private $hasOne = array();
    
    /**
     * One-to-many entity relationships.
     * 
     * @var array
     */
    private $hasMany = array();
    
    /**
     * Mapped setters.
     * 
     * @var array
     */
    private $setters = array();
    
    /**
     * Mapped getters.
     * 
     * @var array
     */
    private $getters = array();
    
    /**
     * Mapped autoloaders.
     * 
     * @var array
     */
    private $autoloaders = array();
    
    /**
     * Constructs a new entity and sets any passed values.
     * 
     * @param mixed $vals The values to set.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function __construct($values = array())
    {
        $this->init();
        $this->import($values);
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
        if ($this->canAccessProperty($name)) {
            if (isset($this->setters[$name])) {
                $setter = $this->setters[$name];
                $this->$setter($value);
            } else {
                $this->set($name, $value);
            }
        }
        return $this;
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
        if (!$this->canAccessProperty($name)) {
            return null;
        }
        
        // autoload if it's not set and an autoloader is registered for it
        if (!$this->__isset($name) && $this->hasAutoloader($name)) {
            $this->autoload($name);
        }
        
        if (isset($this->getters[$name])) {
            $getter = $this->getters[$name];
            return $this->$getter();
        }
        
        return $this->get($name);
    }
    
    /**
     * For easy property checking.
     * 
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    
    /**
     * For easy property unsetting.
     * 
     * @param string $name The value to unset.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
    }
    
    /**
     * Maps the specified property to a setter method.
     * 
     * @param string $name   The name of the property.
     * @param string $method The name of the method.
     * 
     * @return Entity
     */
    public function mapSetter($name, $method)
    {
        $this->setters[$name] = $method;
        return $this;
    }
    
    /**
     * Maps the specified property to a getter method.
     * 
     * @param string $name   The name of the property.
     * @param string $method The name of the method.
     * 
     * @return Entity
     */
    public function mapGetter($name, $method)
    {
        $this->getters[$name] = $method;
        return $this;
    }
    
    /**
     * Maps an autoloader method to the specified method. The mapped method should explicitly set the values using
     * get() or set().
     * 
     * @param string $name   The name of the property.
     * @param string $method The name of the method.
     * 
     * @return mixed
     */
    public function mapAutoloader($name, $method)
    {
        if (!is_string($method)) {
            throw new Exception('The specified autoloader for {$name} must be a string.');
        }
        
        $this->autoloaders[$name] = $method;
        return $this;
    }
    
    /**
     * Returns whether or not the specified autoloader exists.
     * 
     * @param string $name The name of the autoloader.
     * 
     * @return bool
     */
    public function hasAutoloader($name)
    {
        return isset($this->autoloaders[$name]);
    }
    
    /**
     * Returns the specified autoloader.
     * 
     * @param string $name The name of the autoloader.
     * 
     * @throws Exception If the specified autoloader does not exist.
     * 
     * @return string
     */
    public function getAutoloader($name)
    {
        if (!$this->hasAutoloader($name)) {
            throw new Exception("Cannot get autoloader for {$name} because the autoloader was not defined.");
        }
        
        return $this->autoloaders[$name];
    }
    
    /**
     * Autoloads the specified item.
     * 
     * @param string $name The name of the autoloader.
     * 
     * @throws Exception If the specified autoloader does not exist.
     * 
     * @return Entity
     */
    public function autoload($name)
    {
        if (!$this->hasAutoloader($name)) {
            throw new Exception("Cannot autoload {$name} because an autoloader for it was not defined.");
        }
        
        $autoloader = $this->getAutoloader($name);
        $this->$autoloader();
        return $this;
    }
    
    /**
     * Autoloads all registered autoloaders.
     * 
     * @return Entity
     */
    public function autoloadAll()
    {
        foreach ($this->autoloaders as $name => $autoloader) {
            $this->autoload($name);
        }
        return $this;
    }
    
    /**
     * Adds a has-one relationship to the entity.
     * 
     * @param string $name  The name of the property.
     * @param string $class The class to use.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function hasOne($name, $class)
    {
        $this->hasOne[$name] = $class;
        return $this;
    }
    
    /**
     * Adds a has-many relationship to the entity.
     * 
     * @param string $name  The name of the property.
     * @param string $class The class to pass to the Set.
     */
    public function hasMany($name, $class)
    {
        $this->hasMany[$name] = $class;
        return $this;
    }
    
    /**
     * Whitelists a property or properties.
     * 
     * @param mixed $properties A property or array of properties to whitelist.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function whitelist($properties)
    {
        foreach ((array) $properties as $property) {
            $this->whitelist[$property] = $property;
        }
        return $this;
    }
    
    /**
     * Blacklists a property or properties.
     * 
     * @param mixed $properties A property or array of properties to blacklist.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function blacklist($properties)
    {
        foreach ((array) $properties as $property) {
            $this->blacklist[$property] = $property;
        }
        return $this;
    }
    
    /**
     * Fills the entity with the specified values.
     * 
     * @param mixed $array The array to import.
     * 
     * @return \Model\Entity\EntityAbstract
     */
    public function import($array)
    {
        if (is_array($array) || is_object($array)) {
            foreach ($array as $k => $v) {
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
    public function export()
    {
        $array = array();
        foreach ($this->data as $k => $v) {
            if ($v instanceof AccessibleInterface) {
                $v = $v->export();
            }
            $array[$k] = $v;
        }
        return $array;
    }
    
    /**
     * For setting properties like an array.
     * 
     * @param string $name  The property to set.
     * @param mixed  $value The value to set.
     * 
     * @return \Model\Entity\EntityAbstract
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
     * @return \Model\Entity\EntityAbstract
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
     * @return \Model\Entity\EntityAbstract
     */
    public function next()
    {
        next($this->data);
        return $this;
    }
    
    /**
     * Resets the iteration.
     * 
     * @return \Model\Entity\EntityAbstract
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
        return serialize($this->export());
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
        $this->import(unserialize($data));
    }
    
    /**
     * Allows an item to be set directly into the data array without any filtering.
     * 
     * @param string $name  The name of the property.
     * @param mixed  $value The value of the property.
     * 
     * @return Entity
     */
    protected function set($name, $value)
    {
        if (isset($this->hasOne[$name]) && !$value instanceof Entity) {
            $class = $this->hasOne[$name];
            $this->data[$name] = new $class($value);
        } elseif (isset($this->hasMany[$name]) && !$value instanceof Set) {
            $this->data[$name] = new Set($this->hasMany[$name], $value);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Allows an item's value to be retrieved directly without any filtering.
     * 
     * @param string $name The name of the property.
     * 
     * @return mixed
     */
    protected function get($name)
    {
        // if value exists, just return it
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        
        // relationships
        if (isset($this->hasOne[$name])) {
            $class = $this->hasOne[$name];
            $this->data[$name] = new $class;
            return $this->data[$name];
        } elseif (isset($this->hasMany[$name])) {
            $this->data[$name] = new Set($this->hasMany[$name]);
            return $this->data[$name];
        }
        
        return null;
    }
    
    /**
     * Checks to see if the property can be set according to whitelist/blacklist restrictions.
     * 
     * @param string $name The property to check.
     * 
     * @return bool
     */
    private function canAccessProperty($name)
    {
        if ($this->whitelist && !isset($this->whitelist[$name])) {
            return false;
        }
        
        if (isset($this->blacklist[$name])) {
            return false;
        }
        
        return true;
    }
}
