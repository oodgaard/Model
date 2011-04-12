<?php

namespace Model;

/**
 * The main model exception class.
 * 
 * @category Exceptions
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Mapper implements \IteratorAggregate
{
    /**
     * The input data.
     * 
     * @var array
     */
    private $data = array();
    
    /**
     * Constructs a new mapper and passes in the data to be mapped.
     * 
     * @param mixed $data The input data.
     * 
     * @return \Model\Mapper
     */
    public function __construct($data = array())
    {
        $this->fill($data);
        $this->init();
    }
    
    /**
     * Initialization hook for extending classes.
     * 
     * @return void
     */
    public function init()
    {
        
    }
    
    /**
     * Fills the mapper with data to map.
     * 
     * @param mixed $data The input data.
     * 
     * @return \Model\Mapper
     */
    public function fill($data)
    {
        if (!is_array($data) && !is_object($data)) {
            throw new Exception('The passed data must be traversable.');
        }
        
        foreach ($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }
    
    /**
     * Clears the data in the mapper.
     * 
     * @return \Model\Mapper
     */
    public function clear()
    {
        $this->data = array();
        return $this;
    }
    
    /**
     * Maps an input key to an output key. Dot notation is used to denote hierarchy.
     * 
     * @param string $from The input key.
     * @param mixed  $tos  The output key or array of keys.
     * 
     * @return \Model\Mapper
     */
    public function map($from, $tos)
    {
        foreach ((array) $tos as $to) {
            $this->map[] = array('from' => $from, 'to' => $to);
        }
        return $this;
    }
    
    /**
     * Converts the input array to the output array.
     * 
     * @return array
     */
    public function convert()
    {
        $array = array();
        foreach ($this->map as $map) {
            $this->setMappedValue(
                $map['to'],
                $this->getMappedValue($map['from'], $this->data),
                $array
            );
        }
        return $array;
    }
    
    /**
     * Returns the elements in the iterator. SplFixedArray is used because it has a very small memory footprint.
     * 
     * @return SplFixedArray
     */
    public function getIterator()
    {
        return new SplFixedArray($this->convert());
    }
    
    /**
     * Maps the value specified value from $from to $to and returns the resulting array.
     * 
     * @param string $map  The from key.
     * @param string $from The array to get the value from.
     * 
     * @return mixed
     */
    private function getMappedValue($map, array $from = array())
    {
        // only get the first dot part and the rest still intact
        // this way we can tell if we are at the end
        $parts = explode('.', $map, 2);
        
        // if we are NOT at the end of the dot-notated string we continue
        // to return the mapped value using the rest of the dot parts
        // otherwise, we attempt to return the mapped value if it is set
        if (isset($parts[1]) && isset($from[$parts[0]])) {
            return $this->getMappedValue($parts[1], $from[$parts[0]]);;
        } elseif (isset($from[$parts[0]])) {
            return $from[$parts[0]];
        }
        
        // by default, null is always returned
        return null;
    }
    
    /**
     * Sets the specified value using the given map to the specified array.
     * 
     * @param string $map   The map to use to set the value in the array.
     * @param mixed  $value The the value to map.
     * @param array  &$to   The array that we are mapping to.
     * 
     * @return \Model\Mapper
     */
    private function setMappedValue($map, $value, array &$to = array())
    {
        // only 2 parts at a time
        $parts = explode('.', $map, 2);
        
        // check if we are at the end
        // if not, continue to set
        if (isset($parts[1])) {
            $this->modifyKey($parts[0], $to);
            if (!isset($to[$parts[0]])) {
                $to[$parts[0]] = array();
            }
            $this->setMappedValue($parts[1], $value, $to[$parts[0]]);
        } else {
            $this->modifyKey($parts[0], $to);
            $this->modifyArray($parts[0], $value, $to);
        }
        
        // since we modify a reference, we can chain if we want
        return $this;
    }
    
    /**
     * Modifies the array based on the input key and value.
     * 
     * @param string $key   The key to modify.
     * @param mixed  $value The value to modify it with.
     * @param array  &$to   The array being modified.
     * 
     * @return \Model\Mapper;
     */
    private function modifyArray($key, $value, array &$to)
    {
        $to[$key] = $value;
        return $this;
    }
    
    /**
     * Detects the type of key and modifies it according to its type. We have to pass in the array that we are mapping
     * to because we need information about it when we are detecting the key and modifying it.
     * 
     * @param mixed &$key The key to modify.
     * @param array $to   The array we are mapping to so we can gather information about it.
     * 
     * @return \Model\Mapper
     */
    private function modifyKey(&$key, array $to)
    {
        if ($key === '$') {
            $key = count($to);
        } elseif (is_numeric($key)) {
            $key = (int) $key;
        }
        return $this;
    }
}