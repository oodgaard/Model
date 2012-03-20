<?php

namespace Model\Repository;
use Model\Cache\CacheInterface;

/**
 * The main repository interface. All model repositories must implement this.
 * 
 * @category Repositories
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
abstract class RepositoryAbstract
{
    /**
     * The cache driver to use, if any.
     * 
     * @var CacheInterface
     */
    private $cache;
    
    /**
     * Sets the cache interface to use.
     * 
     * @param CacheInterface $cache The cache interface to use.
     * 
     * @return RepositoryAbstract
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }
    
    /**
     * Returns the cache interface being used.
     * 
     * @return CacheInterface|null
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    /**
     * Persists data for the current repository method.
     * 
     * @param mixed $item The item to store.
     * @param mixed $time The time to store the item for.
     * 
     * @return RepositoryAbstract
     */
    protected function persist($item, $time = null)
    {
        return $this->persistFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs(), $item, $time);
    }
    
    /**
     * Retrieves the item for the current repository method.
     * 
     * @return mixed
     */
    protected function retrieve()
    {
        return $this->retrieveFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Expires the item for the current repository method.
     * 
     * @return RepositoryAbstract
     */
    protected function expire()
    {
        return $this->expireFor($this->getLastClass(), $this->getLastMethod(), $this->getLastArgs());
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
    protected function persistFor($class, $method, array $args, $item, $time = null)
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
    protected function retrieveFor($class, $method, array $args)
    {
        if ($this->cache) {
            return $this->cache->get($this->generateCacheKey($class, $method, $args));
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
    protected function expireFor($class, $method, array $args)
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
     * Returns the last repository class that was called. This class is designed to be called from the "persist()",
     * "retrieve()" or "expire()" methods.
     * 
     * @return string
     */
    private function getLastClass()
    {
        $callstack = debug_backtrace();
        return get_class($callstack[2]['object']);
    }
    
    /**
     * Returns the last repository method that was called. This method is designed to be called from the "persist()",
     * "retrieve()" or "expire()" methods.
     * 
     * @return string
     */
    private function getLastMethod()
    {
        $callstack = debug_backtrace();
        return $callstack[2]['function'];
    }
    
    /**
     * Returns the arguments that were passed to the last called repository method. This method is designed to be
     * called from the "persist()", "retrieve()" or "expire()" methods.
     * 
     * @return array
     */
    private function getLastArgs()
    {
        $callstack = debug_backtrace();
        return $callstack[2]['args'];
    }
}