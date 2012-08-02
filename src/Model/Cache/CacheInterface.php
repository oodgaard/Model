<?php

namespace Model\Cache;

/**
 * The cache driver interface.
 * 
 * @category Cache
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface CacheInterface
{
    /**
     * Caches an item.
     * 
     * @param string $key      The cache key.
     * @param mixed  $value    The cached value.
     * @param mixed  $lifetime The max lifetime of the item in the cache.
     * 
     * @return CacheInterface
     */
    public function set($key, $value, $lifetime = null);
    
    /**
     * Returns a cached item.
     * 
     * @param string $key The cache key.
     * 
     * @return mixed
     */
    public function get($key);
    
    /**
     * Checks to see if the specified cache item exists.
     * 
     * @param string $key The key to check for.
     * 
     * @return bool
     */
    public function has($key);
    
    /**
     * Removes the item with the specified key.
     * 
     * @param string $key The key of the item to remove.
     * 
     * @return CacheInterface
     */
    public function remove($key);
    
    /**
     * Clears the whole cache.
     * 
     * @return CacheInterface
     */
    public function clear();
}