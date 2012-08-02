<?php

namespace Model\Cache;
use Model\Exception;

/**
 * A cache handler that can use multiple cache sources.
 * 
 * @category Cache
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Chain implements CacheInterface
{
    /**
     * The cache sources being used.
     * 
     * @var array
     */
    private $drivers = array();
    
    /**
     * Constructs a new cache driver chain.
     * 
     * @param array $drivers The drivers to add.
     * 
     * @return Chain
     */
    public function __construct(array $drivers = [])
    {
        foreach ($drivers as $driver) {
            $this->add($driver);
        }
    }
    
    /**
     * Adds a driver to the chain.
     * 
     * @param CacheInterface $driver The cache driver to add.
     * 
     * @return Chain
     */
    public function add(CacheInterface $driver)
    {
        $this->drivers[] = $driver;
        return $this;
    }
    
    /**
     * Adds a cache item to the drivers.
     * 
     * @param string $key      The cache key.
     * @param mixed  $value    The cached value.
     * @param mixed  $lifetime The max lifetime of the item in the cache.
     * 
     * @return Chain
     */
    public function set($key, $value, $lifetime = null)
    {
        foreach ($this->drivers as $cache) {
            $cache->set($key, $value, $lifetime);
        }
        return $this;
    }
    
    /**
     * Returns the first matched item from the cache.
     * 
     * @param string $key The cache key.
     * 
     * @return mixed
     */
    public function get($key)
    {
        foreach ($this->drivers as $cache) {
            if ($value = $cache->get($key)) {
                return $value;
            }
        }
    }
    
    /**
     * Checks to see if the specified cache item exists.
     * 
     * @param string $key The key to check for.
     * 
     * @return bool
     */
    public function exists($key)
    {
        foreach ($this->drivers as $cache) {
            if ($cache->exists($key)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Removes the item with the specified key.
     * 
     * @param string $key The key of the item to remove.
     * 
     * @return Chain
     */
    public function remove($key)
    {
        foreach ($this->drivers as $cache) {
            $cache->remove($key);
        }
        return $this;
    }
    
    /**
     * Clears the whole cache.
     * 
     * @return Chain
     */
    public function clear()
    {
        foreach ($this->drivers as $driver) {
            $driver->clear();
        }
        return $this;
    }
}