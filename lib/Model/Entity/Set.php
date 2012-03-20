<?php

namespace Model\Entity;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * The class that represents a set of entities.
 * 
 * @category Entities
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Set implements AccessibleInterface
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
     * @param mixed  $values The values to apply.
     * @param string $class  The class that represents the entities. By default, the set uses the class of the first
     *                       object it stores.
     * 
     * @return Set
     */
    public function __construct($values = array(), $class = null)
    {
        if ($class) {
            $this->setClass($class);
        }
        
        $this->fill($values);
    }
    
    /**
     * Creates a new item with the specified data.
     * 
     * @param mixed $from The data to create the entity from, if any.
     * 
     * @return Entity
     */
    public function create($from = array())
    {
        $class = $this->class;
        return new $class($from);
    }

    /**
     * Sets the namespace to use.
     * 
     * @param string $ns The namespace to use.
     * 
     * @return Set
     */
    public function setNamespace($ns)
    {
        return $this->setClass($ns . '\\' . basename($this->class));
    }

    /**
     * Returns the namespace the set should use.
     * 
     * @return string
     */
    public function getNamespace()
    {
        return dirname($this->class);
    }

    /**
     * Sets the class to use.
     * 
     * @param mixed $class The class to use.
     * 
     * @return Set
     */
    public function setClass($class)
    {
        $this->class = $this->makeFullyQualifiedClass($class);
        return $this;
    }
    
    /**
     * Returns the class being used for this set instance.
     * 
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Returns whether or not the set represents the specified class.
     * 
     * @param string $class The class name to check against.
     * 
     * @return bool
     */
    public function isRepresenting($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return $this->class === $this->makeFullyQualifiedClass($class);
    }
    
    /**
     * Checks to see if the set represents the specified class. If not, then an exception is thrown.
     * 
     * @throws \Model\Exception If the set does not represent the specified class.
     * 
     * @param string $class The class to check.
     * 
     * @return Set
     */
    public function mustRepresent($class)
    {
        if (!$this->isRepresenting($class)) {
            $class = is_object($class) ? get_class($class) : $class;
            throw new RuntimeException(
                'The entity set is representing "'
                . $this->class
                . '" not "'
                . $class
                . '".'
            );
        }
        return $this;
    }
    
    /**
     * Fills values from a traversable item.
     * 
     * @param mixed $traversable The values to import.
     * 
     * @return Entity
     */
    public function fill($traversable)
    {
        // make sure the item is iterable
        if (!is_array($traversable) && !is_object($traversable)) {
            throw new InvalidArgumentException('Item being imported must traversable.');
        }

        // now apply the values
        foreach ($traversable as $k => $v) {
            $this->offsetSet($k, $v);
        }
        
        return $this;
    }
    
    /**
     * Converts the set to an array.
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $k => $v) {
            $array[$k] = $v->export();
        }
        return $array;
    }
    
    /**
     * Resets the data array.
     * 
     * @return Set
     */
    public function clean()
    {
        $this->data = array();
        return $this;
    }
    
    /**
     * Returns whether or not the specified accessible is clean.
     * 
     * @return bool
     */
    public function isClean()
    {
        return count($this->data) === 0;
    }
    
    /**
     * Validates each item in the set.
     * 
     * @throws ValidatorException If an item is not valid.
     * 
     * @return Set
     */
    public function validate()
    {
        foreach ($this->data as $item) {
            $item->validate();
        }
        return $this;
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
        foreach ($this as $entity) {
            $cb = is_string($callback) ? array($entity, $callback) : $callback;
            call_user_func($cb, $userdata ? $userdata : $entity);
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
     * Moves the item at the specified $currentIndex to the $newIndex and shifts any elements at the $newIndex forward.
     * 
     * @param int $currentIndex The current index.
     * @param int $newIndex     The new index.
     * 
     * @return Set
     */
    public function moveTo($currentIndex, $newIndex)
    {
        if ($item = $this->offsetGet($currentIndex)) {
            $this->offsetUnset($currentIndex);
            $this->push($newIndex, $item);
        }
        return $this;
    }
    
    /**
     * Inserts an item at the specified offset.
     * 
     * @param mixed $item The item to insert.
     * 
     * @return Set
     */
    public function push($index, $item)
    {
        $start = array_slice($this->data, 0, $index);
        $end   = array_slice($this->data, $index);
        
        $this->validateEntity($item);
        
        $this->data = array_merge($start, array($item), $end);
        
        return $this;
    }
    
    /**
     * Pulls an item from the current collection and returns it.
     * 
     * @param int $index The item to pull out of the current set and return.
     * 
     * @return Entity
     */
    public function pull($index)
    {
        if ($item = $this->offsetGet($index)) {
            $this->offsetUnset($index);
            return $item;
        }
        return null;
    }
    
    /**
     * Prepends an item.
     * 
     * @param mixed $item The item to prepend.
     * 
     * @return Set
     */
    public function prepend($item)
    {
        return $this->push(0, $item);
    }
    
    /**
     * Appends an item.
     * 
     * @param mixed $item The item to append.
     * 
     * @return Set
     */
    public function append($item)
    {
        return $this->push($this->count(), $item);
    }
    
    /**
     * Reduces the array down to the items that match the specified array of keys. If the specified keys are empty
     * 
     * @param array $keys The keys to reduce the array down to.
     * 
     * @return Set
     */
    public function reduce($keys)
    {
        // find items that exist
        $found = array();
        foreach ((array) $keys as $key) {
            if (isset($this->data[$key])) {
                $found[$key] = $key;
            }
        }
        
        // if nothing is found, clear all items and return
        if (!$found) {
            return $this->clear();
        }
        
        // remove all non-existing items
        foreach ($this->data as $key => $value) {
            if (!isset($found[$key])) {
                unset($this->data[$key]);
            }
        }
        
        // re-index
        $this->data = array_values($this->data);
        return $this;
    }
    
    /**
     * Empties the current set.
     * 
     * @return Set
     */
    public function clear()
    {
        $this->data = array();
        return $this;
    }
    
    /**
     * Finds items matching the specified criteria and returns a new set of them.
     * 
     * @param array $query  An array of name/value pairs of fields to match.
     * @param int   $limit  The limit of items to find.
     * @param int   $offset The offset to start looking at.
     * 
     * @return array
     */
    public function findOne(array $query)
    {
        $clone = clone $this;
        $key   = $clone->findKey($query);
        if ($key !== false) {
            return $clone->reduce($key)->offsetGet(0);
        }
        return false;
    }
    
    /**
     * Returns the first matched entity.
     * 
     * @param array $query An array of name/value pairs of fields to match.
     * 
     * @return Entity
     */
    public function find(array $query, $limit = 0, $offset = 0)
    {
        $clone = clone $this;
        return $clone->reduce($clone->findKeys($query, $limit, $offset));
    }
    
    /**
     * Returns the first matched item.
     * 
     * @param array $query An array of name/value pairs of fields to match.
     * 
     * @return Entity
     */
    public function findKey(array $query)
    {
        if ($found = $this->findKeys($query, 1)) {
            return $found[0];
        }
        return false;
    }
    
    /**
     * Finds items matching the specified criteria and returns an array of their indexes.
     * 
     * @param array $query  An array of name/value pairs of fields to match.
     * @param int   $limit  The limit of items to find.
     * @param int   $offset The offset to start looking at.
     * 
     * @return array
     */
    public function findKeys(array $query, $limit = 0, $offset = 0)
    {
        $items = array();
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
                $items[] = $key;
            }
        }
        return $items;
    }
    
    /**
     * Returns the first element without setting the pointer to it.
     * 
     * @return Entity
     */
    public function first()
    {
        if ($this->offsetExists(0)) {
            return $this->offsetGet(0);
        }
        return null;
    }
    
    /**
     * Returns the first element without setting the pointer to it.
     * 
     * @return Entity
     */
    public function last()
    {
        $lastIndex = $this->count() - 1;
        if ($this->offsetExists($lastIndex)) {
            return $this->offsetGet($lastIndex);
        }
        return null;
    }
    
    /**
     * Adds or sets an entity in the set. The value is set directly. Only in offsetGet() is the
     * entity instantiated and the value passed to it and then re-set.
     * 
     * @param mixed $offset The offset to set.
     * @param mixed $value  The value to set.
     * 
     * @return Entity
     */
    public function offsetSet($offset, $value)
    {
        $this->validateEntity($value);
        
        $offset = is_null($offset) ? count($this->data) : $offset;
        
        $this->data[$offset] = $value;
        
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
     * @return Entity
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
            $this->data = array_values($this->data);
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
     * @return Entity
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
     * @return Entity
     */
    public function next()
    {
        next($this->data);
        return $this;
    }
    
    /**
     * Resets to the first element.
     * 
     * @return Entity
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
     * Ensures the item is a valid entity instance.
     * 
     * @param Entity $item The entity to validate.
     * 
     * @throws UnexpectedValueException If the item is not an object.
     * @throws UnexpectedValueException If the item is not a valid instance.
     * 
     * @return void
     */
    private function validateEntity($item)
    {
        if (!is_object($item)) {
            throw new UnexpectedValueException('The item passed into "' . get_class($this) . '" is not an object.');
        }
        
        if (!$this->class) {
            $this->class = get_class($item);
        }
        
        if (!$item instanceof $this->class) {
            throw new UnexpectedValueException(
                'The item "'
                . get_class($item)
                . '" must be an instance of "'
                . $this->class
                . '".'
            );
        }
    }
    
    /**
     * Makes the specified class into a fully qualified class name.
     * 
     * @param string $class The class.
     * 
     * @return string
     */
    private function makeFullyQualifiedClass($class)
    {
        if (!$class) {
            throw new UnexpectedValueException('Cannot make a fully qualified class out of an empty value.');
        }
        
        if (is_object($class)) {
            $class = get_class($class);
        } elseif (!is_string($class)) {
            throw new InvalidArgumentException(
                'Cannot make fully qualfied class name from argument because it is not an object or string.'
            );
        }
        
        $ns    = dirname($class);
        $ns    = trim($ns, '\\');
        $ns    = $ns ? '\\' . $ns : '';
        $class = basename($class);
        $class = trim($class, '\\');
        $class = $ns . '\\' . $class;
        
        return $class;
    }

    /**
     * Sets the default namespace to use.
     * 
     * @param string $ns The default namespace to use.
     * 
     * @return void
     */
    public static function setDefaultNamespace($ns)
    {
        static::$defaultNs = $ns;
    }
}