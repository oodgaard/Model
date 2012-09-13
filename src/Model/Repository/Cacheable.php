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
     * Caches for specific methods.
     * 
     * @var array
     */
    private $cacheDrivers = [];

    /**
     * Specific lifetimes for each method.
     * 
     * @var array
     */
    private $cacheLifetimes = [];
    
    /**
     * Automatically decides what to do with the cache. All protected methods are considered methods where caching can
     * be automated. If a method is private, it is not called.
     * 
     * @param string $name The method name.
     * @param array  $args The method args.
     * 
     * @return mixed
     */
    public function __call($name, array $args = [])
    {
        $class = get_called_class();
        
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
                'In order to automate the caching of "%s->%s()", you must mark it as protected.',
                $class,
                $name
            ));
        }
        
        // if it already exists in the cache, just return it
        if ($this->hasCache($name, $args)) {
            return $this->getCache($name, $args);
        }
        
        // get the value of the method
        $value = call_user_func_array([$this, $name], $args);
        
        // cache it
        $this->setCache($name, $args, $value);
        
        // return it
        return $value;
    }
    
    /**
     * Sets the cache interface to use.
     * 
     * @param string         $method   The method to apply the driver to.
     * @param CacheInterface $driver   The cache interface to use.
     * @param int | null     $lifetime The default lifetime of this method.
     * 
     * @return Cacheable
     */
    public function setCacheDriver($method, CacheInterface $driver, $lifetime = null)
    {
        $this->cacheDrivers[$method]   = $driver;
        $this->cacheLifetimes[$method] = $lifetime;
        return $this;
    }
    
    /**
     * Returns the appropriate cache driver.
     * 
     * @param string $method The method, if any, to get the cache driver for.
     * 
     * @return CacheInterface | null
     */
    public function getCacheDriver($method)
    {
        if (isset($this->cacheDrivers[$method])) {
            return $this->cacheDrivers[$method];
        }
    }
    
    /**
     * Caches the specified method.
     * 
     * @param string $method    The method to cache for.
     * @param array  $args      The arguments to cache for.
     * @param mixed  $value     The value to cache.
     * @param int    $limfetime The lifetime of the cache.
     * 
     * @return Cacheable
     */
    public function setCache($method, array $args, $value, $lifetime = null)
    {
        if ($driver = $this->getCacheDriver($method)) {
            if ($lifetime === null && isset($this->cacheLifetimes[$method])) {
                $lifetime = $this->cacheLifetimes[$method];
            }
            return $driver->set($this->generateCacheKey($method, $args), $value, $lifetime);
        }
        return $this;
    }
    
    /**
     * Provides a way to cache a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return mixed
     */
    public function getCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            return $driver->get($this->generateCacheKey($method, $args));
        }
        return false;
    }
    
    /**
     * Provides a way to check the cache for a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return bool
     */
    public function hasCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            return $driver->has($this->generateCacheKey($method, $args));
        }
        return false;
    }
    
    /**
     * Provides a way to expire a method cache other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return Cacheable
     */
    public function removeCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            $driver->remove($this->generateCacheKey($method, $args));
        }
        return $this;
    }
    
    /**
     * Clears all cache in this repository.
     * 
     * @return Cacheable
     */
    public function clearCache()
    {
        foreach ($this->cacheDrivers as $driver) {
            $driver->clear();
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
    private function generateCacheKey($method, array $args)
    {
        return md5(get_class() . $method . serialize($args));
    }
}