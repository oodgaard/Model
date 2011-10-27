<?php

namespace Model\Mapper;

/**
 * The mapping class that maps one array or object to an array.
 * 
 * @category Mapping
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Mapper
{
    /**
     * The internal mapping to convert the input data to.
     * 
     * @var array
     */
    private $map = array();
    
    /**
     * The name of the element that will be used for the top level array's key.
     * 
     * @var string
     */
    private $key;
    
    /**
     * Intializes a new mapper. Any mapping passed in here is passed off to the map method.
     * 
     * @param array $map The mapping to pass to the map method.
     * 
     * @return \Model\Mapper
     */
    public function __construct(array $map = array())
    {
        $this->init();
        $this->map($map);
    }
    
    /**
     * Initializes the mapper. Good for mapper extensions for setting up an initial mapping.
     * 
     * @return void
     */
    public function init()
    {
        
    }
    
    /**
     * Maps an input key to an output key. Dot notation is used to denote hierarchy.
     * 
     * @param string $from The input key.
     * @param mixed  $to   The output key or array of keys.
     * 
     * @return \Model\Mapper
     */
    public function map($from, $to = null)
    {
        if (!is_array($from)) {
            $from = array($from => $to);
        }
        
        foreach ($from as $mapFrom => $mapTo) {
            foreach ((array) $mapTo as $subMapTo) {
                $this->map[] = array('from' => $mapFrom, 'to' => $subMapTo);
            }
        }
        
        return $this;
    }
    
    /**
     * Sets the element value to use for the top level array's key. By default this is simply the index.
     * 
     * @param string $key The key to use.
     * 
     * @return Mapper
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    /**
     * Returns the name of the element that will be used for the top level array's key.
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * Converts the input array to the output array.
     * 
     * @return array
     */
    public function convert()
    {
        // before is simply all items converted to scalar or array (no objects)
        $before = array();
        
        // after is after the values are mapped and is what is returned
        $after  = array();
        
        // allow multiple arguments
        foreach (func_get_args() as $arg) {
            // we can only map traversible items
            if (!is_array($arg) && !is_object($arg)) {
                continue;
            }
            
            // make sure it's an array so we know how to access each element
            $arg = $this->convertToArray($arg);
            
            // since we allow more than one item to be passed in for conversion, we merge the result
            $before = array_merge($before, $arg);
        }
        
        // applies each mapping to each of the elements in the array
        foreach ($this->map as $map) {
            $this->setMappedValue($map['to'], $this->getMappedValue($map['from'], $before), $after);
        }
        
        return $after;
    }
    
    /**
     * Does a deep convert on multi-dimensional arrays for one or more sets of data. The same as nesting multiple calls
     * to convert() in a loop and building a converted array.
     * 
     * @return array
     */
    public function convertArray()
    {
        // the resulting item is always an array
        $after = array();
        
        // allows more than one array of items
        foreach (func_get_args() as $array) {
            // the item must be traversible
            if (!is_array($array) && !is_object($array)) {
                continue;
            }
            
            // iterate over each entry and convert it
            foreach ($array as $index => $before) {
                $converted = $this->convert($before);
                if ($this->key && $mappedKey = $this->getMappedValue($this->key, $converted)) {
                    if (is_numeric($mappedKey) || is_string($mappedKey)) {
                        $index = $mappedKey;
                    }
                }
                $after[$index] = $converted;
            }
        }
        
        return $after;
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
    
    /**
     * Recursively converts the passed in item to an array. It assumes the item is an array or object.
     * 
     * @param mixed $item The item to convert.
     * 
     * @return array
     */
    private function convertToArray($item)
    {
        $array = array();
        foreach ($item as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $array[$key] = $this->convertToArray($value);
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }
}
