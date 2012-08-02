<?php

namespace Model\Repository;
use InvalidArgumentException;
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
     * The fallback cache driver to use if one is not found in the method caches.
     * 
     * @var CacheInterface
     */
    private $cacheDriver;
    
    /**
     * Caches for specific methods.
     * 
     * @var array
     */
    private $cacheDrivers = [];
    
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
        if ($this->hasCacheFor($name, $args)) {
            return $this->getCacheFor($name, $args);
        }
        
        // get the value of the method
        $value = call_user_func_array([$this, $name], $args);
        
        // cache it
        $this->setCacheFor($name, $args, $value);
        
        // return it
        return $value;
    }
    
    /**
     * Sets the cache interface to use.
     * 
     * @param string         $method The method to apply the driver to.
     * @param CacheInterface $driver The cache interface to use.
     * 
     * @return RepositoryAbstract
     */
    public function setCacheDriver($method, CacheInterface $driver)
    {
        $this->cacheDrivers[$method] = $driver;
        return $this;
    }
    
    /**
     * Applies a cache driver to multiple methods.
     * 
     * @param array          $methods The methods to apply the driver to.
     * @param CacheInterface $driver  The cache interface to use.
     * 
     * @return Cacheable
     */
    public function setCacheDrivers(array $methods, CacheInterface $driver)
    {
        foreach ($methods as $method) {
            $this->cacheDrivers[$method] = $driver;
        }
        return $this;
    }
    
    /**
     * Sets the default cache driver.
     * 
     * @param CacheInterface $driver The cache interface to use.
     * 
     * @return Cacheable
     */
    public function setDefaultCacheDriver(CacheInterface $driver)
    {
        $this->cacheDriver = $driver;
        return $this;
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
    private function setCacheFor($method, array $args, $item, $time = null)
    {
        if ($driver = $this->resolveCacheDriverFor($method)) {
            $driver->set($this->generateCacheKey($method, $args), $item, $time);
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
    private function getCacheFor($method, array $args)
    {
        if ($driver = $this->resolveCacheDriverFor($method)) {
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
     * @return RepositoryAbstract
     */
    private function hasCacheFor($method, array $args)
    {
        if ($driver = $this->resolveCacheDriverFor($method)) {
            return $driver->exists($this->generateCacheKey($method, $args));
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
    private function clearCacheFor($method, array $args)
    {
        if ($driver = $this->resolveCacheDriverFor($method)) {
            $driver->remove($this->generateCacheKey($method, $args));
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
    
    /**
     * Returns the appropriate cache driver.
     * 
     * @param string $method The method, if any, to get the cache driver for.
     * 
     * @return CacheInterface | null
     */
    private function resolveCacheDriverFor($method = null)
    {
        return isset($this->cacheDrivers[$method]) ? $this->cacheDrivers[$method] : $this->cacheDriver;
    }
}