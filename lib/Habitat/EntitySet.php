<?php

namespace Habitat;

/**
 * The class that represents a set of entities.
 * 
 * @category Entities
 * @package  Habitat
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class EntitySet implements Accessible
{
    /**
     * The class used to represent each entity in the set.
     * 
     * @var string
     */
    private $class;
    
    /**
     * The data containing each entity.
     * 
     * @var array
     */
    private $data = array();
    
    /**
     * Constructs a new entity set. Primarily used for has many relations.
     * 
     * @param string $class  The class that represents the entities.
     * @param mixed  $values The values to apply.
     * 
     * @return \Habitat\EntitySet
     */
    public function __construct($class, $values = array())
    {
        $this->class = $class;
        $this->import($values);
    }
    
    /**
     * Fills values from an array.
     * 
     * @param mixed $array The values to import.
     * 
     * @return \Habitat\Entity
     */
    public function import($array)
    {
        // make sure the item is iterable
        if (!is_array($array) && !is_object($array)) {
            throw new Exception('Item being imported must be an array or object.');
        }
        
        // now apply the values
        foreach ($array as $k => $v) {
            $this->offsetSet($k, $v);
        }
        
        return $this;
    }
    
    /**
     * Fills the entity with the specified values.
     * 
     * @param mixed $vals The values to automate the setting of.
     * 
     * @return \Habitat\Entity
     */
    public function export()
    {
        $array = array();
        foreach ($this as $k => $v) {
            $array[$k] = $v->export();
        }
        return $array;
    }
    
    /**
     * Executes the specified callback on each item and places the return value in an array and returns it. If $userdata
     * is passed, it is used in lieu of passing the entity as the first argument just in case a different order of
     * of parameters need to be passed in.
     * 
     * @param mixed $callback A callable callback.
     * @param array $userdata Any userdata to pass to the callback.
     * 
     * @return mixed
     */
    public function walk($callback, array $userdata = array())
    {
        if (!is_callable($callback)) {
            throw new Exception('The callback specified to \Model\Entity->walk() is not callable.');
        }
        foreach ($this as $entity) {
            call_user_func($callback, $userdata ? $userdata : $entity);
        }
        return $this;
    }
    
    /**
     * Aggregates an array of values for the specified field.
     * 
     * @param string $field The field to aggregate values for.
     * 
     * @return array
     */
    public function aggregate($field)
    {
        $values = array();
        foreach ($this as $item) {
            $values[] = $item->__get($field);
        }
        return $values;
    }
    
    /**
     * Finds item matching the specified criteria and returns an EntitySet of them.
     * 
     * @param array $query  An array of name/value pairs of fields to match.
     * @param int   $limit  The limit of items to find.
     * @param int   $offset The offset to start looking at.
     * 
     * @return \Habitat\EntitySet
     */
    public function find(array $query, $limit = 0, $offset = 0)
    {
        if (!is_array($query)) {
            $query = array($query => $value);
        }
        
        $items = new static($this->class);
        foreach ($this as $key => $item) {
            if ($offset && $offset > $key) {
                continue;
            }
            
            if ($limit && $limit === count($items)) {
                break;
            }
            
            foreach ($query as $name => $value) {
                if (!preg_match('/' . str_replace('/', '\/', $value) . '/', $item->__get($name))) {
                    continue;
                }
                $items[] = $item;
            }
        }
        return $items;
    }
    
    /**
     * Returns the first matched item.
     * 
     * @param array $query An array of name/value pairs of fields to match.
     * 
     * @return \Habitat\Entity
     */
    public function findOne(array $query)
    {
        return $this->find($query, 1)->offsetGet(0);
    }
    
    /**
     * Adds or sets an entity in the set. The value is set directly. Only in offsetGet() is the
     * entity instantiated and the value passed to it and then re-set.
     * 
     * @param mixed $offset The offset to set.
     * @param mixed $value  The value to set.
     * 
     * @return \Habitat\Entity
     */
    public function offsetSet($offset, $value)
    {
        // ensure traversable
        if (!is_array($value) && !is_object($value)) {
            throw new Exception('Item being set onto an EntitySet must be an array or object.');
        }
        
        // detect offset
        $offset = is_null($offset) ? count($this->data) : $offset;
        
        // apply to data
        $this->data[$offset] = $value;
        
        // chain
        return $this;
    }
    
    /**
     * Returns the entity at the specified offset if it exists. If it doesn't exist
     * then it returns null.
     * 
     * At this point, the entity is instantiated so no unnecessary overhead is used.
     * 
     * @param mixed $offset The offset to get.
     * 
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            // if it's not an entity yet, make it one
            // this will allow the set to not take up any overhead if the item is not accessed
            if (!$this->data[$offset] instanceof $this->class) {
                $class               = $this->class;
                $this->data[$offset] = new $class($this->data[$offset]);
            }
            
            // return the value
            return $this->data[$offset];
        }
        return null;
    }
    
    /**
     * Checks to make sure the specified offset exists.
     * 
     * @param mixed $offset The offset to check for.
     * 
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
    
    /**
     * Unsets the specified item at the given offset if it exists.
     * 
     * @param mixed $offset The offset to unset.
     * 
     * @return \Habitat\Entity
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
        return $this;
    }
    
    /**
     * Returns the number of entities in the set.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
    
    /**
     * Returns the current element.
     * 
     * @return \Habitat\Entity
     */
    public function current()
    {
        return $this->offsetGet($this->key());
    }
    
    /**
     * Returns the key of the current element.
     * 
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }
    
    /**
     * Moves to the next element.
     * 
     * @return \Habitat\Entity
     */
    public function next()
    {
        next($this->data);
        return $this;
    }
    
    /**
     * Resets to the first element.
     * 
     * @return \Habitat\Entity
     */
    public function rewind()
    {
        reset($this->data);
        return $this;
    }
    
    /**
     * Returns whether or not another element exists.
     * 
     * @return bool
     */
    public function valid()
    {
        return !is_null($this->key());
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
}