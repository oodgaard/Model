<?php

namespace Model\Repository;
use LogicException;
use Model\Cache\CacheInterface;
use ReflectionMethod;

/**
 * Caching implementation.
 * 
 * @category Repositories
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
trait Cacheable
{
    /**
     * The cache driver to use, if any.
     * 
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * The default cache time.
     * 
     * @var int
     */
    private $cacheTime;
    
    /**
     * Automatically decides what to do with the cache.
     * 
     * @param string $name The method name.
     * @param array  $args The method args.
     * 
     * @return mixed
     */
    public function __call($name, array $args = [])
    {
        $class = get_class();
        
        // if the method does not exist, then there's nothing we can do
        if (!method_exists($this, $name)) {
            throw new LogicException(sprintf(
                'The method "%s" does not exist in "%s".',
                $name,
                $class
            ));
        }
        
        // make sure method is protected
        if (!(new ReflectionMethod($class, $name))->isProtected()) {
            throw new LogicException(sprintf(
                'In order to automate the caching of "%s::%s()", you must mark it as protected.',
                $class,
                $name
            ));
        }
        
        // if it already exists in the cache, just return it
        if ($this->hasCacheFor($class, $name, $args)) {
            return $this->getCacheFor($class, $name, $args);
        }
        
        // get the value of the method
        $value = call_user_func_array([$this, $name], $args);
        
        // cache it
        $this->setCacheFor($class, $name, $args, $value, $this->cacheTime);
        
        // return it
        return $value;
    }
    
    /**
     * Sets the cache interface to use.
     * 
     * @param CacheInterface $cache The cache interface to use.
     * 
     * @return RepositoryAbstract
     */
    public function setCacheDriver(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Returns the cache driver.
     * 
     * @return CacheInterface
     */
    public function getCacheDriver()
    {
        return $this->cache;
    }
    
    /**
     * Sets the cache time for automatic caching.
     * 
     * @param int $time The cache time.
     * 
     * @return Cacheable
     */
    public function setCacheTime($time)
    {
        $this->cacheTime = $time;
        return $this;
    }
    
    /**
     * Returns the cache time.
     * 
     * @return mixed
     */
    public function getCacheTime()
    {
        return $this->cacheTime;
    }
    
    /**
     * Persists data for the current repository method.
     * 
     * @param mixed $item The item to store.
     * @param mixed $time The time to store the item for.
     * 
     * @return RepositoryAbstract
     */
    private function setCache($item, $time = null)
    {
        return $this->setCacheFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs(), $item, $time);
    }
    
    /**
     * Retrieves the item for the current repository method.
     * 
     * @return mixed
     */
    private function getCache()
    {
        return $this->getCacheFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Removes the cache for the current repository method.
     * 
     * @return bool
     */
    private function hasCache()
    {
        return $this->hasCacheFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Expires the item for the current repository method.
     * 
     * @return RepositoryAbstract
     */
    private function clearCache()
    {
        return $this->clearCacheFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Provides a way to cache a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * @param mixed  $item   The item to cache.
     * @param mixed  $time   The time to cache the item for.
     * 
     * @return RepositoryAbstract
     */
    private function setCacheFor($class, $method, array $args, $item, $time = null)
    {
        if ($this->cache) {
            $this->cache->set($this->generateCacheKey($class, $method, $args), $item, $time);
        }
        return $this;
    }
    
    /**
     * Provides a way to cache a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return RepositoryAbstract
     */
    private function getCacheFor($class, $method, array $args)
    {
        if ($this->cache) {
            return $this->cache->get($this->generateCacheKey($class, $method, $args));
        }
        return false;
    }
    
    /**
     * Provides a way to check the cache for a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return RepositoryAbstract
     */
    private function hasCacheFor($class, $method, array $args)
    {
        if ($this->cache) {
            return $this->cache->exists($this->generateCacheKey($class, $method, $args));
        }
        return false;
    }
    
    /**
     * Provides a way to expire a method cache other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return RepositoryAbstract
     */
    private function clearCacheFor($class, $method, array $args)
    {
        if ($this->cache) {
            $this->cache->remove($this->generateCacheKey($class, $method, $args));
        }
        return $this;
    }
    
    /**
     * Generates a cache key for the specified method and arguments.
     * 
     * @param string $method The method to generate the key for.
     * @param array  $args   The arguments passed to the method.
     * 
     * @return string
     */
    private function generateCacheKey($class, $method, array $args)
    {
        return md5($class . $method . serialize($args));
    }
    
    /**
     * Returns the last repository class that was called.
     * 
     * @return string
     */
    private function getLastClass()
    {
        $callstack = debug_backtrace();
        return get_class($callstack[2]['object']);
    }
    
    /**
     * Returns the last repository method that was called.
     * 
     * @return string
     */
    private function getLastMethod()
    {
        $callstack = debug_backtrace();
        return $callstack[2]['function'];
    }
    
    /**
     * Returns the arguments that were passed to the last called repository method.
     * 
     * @return array
     */
    private function getLastArgs()
    {
        $callstack = debug_backtrace();
        return $callstack[2]['args'];
    }
}