<?php

namespace Habitat;

/**
 * The main repository interface. All model repositorys must implement this.
 * 
 * @category Repositories
 * @package  Habitat
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
abstract class Repository
{
    /**
     * The cache driver to use, if any.
     * 
     * @var \Habitat\CacheInterface|null
     */
    private $cache;
    
    /**
     * Constructs a new repository with the specified cache driver.
     * 
     * @param \Habitat\CacheInterface $cache The cache drive to use, if any.
     * 
     * @return \Habitat\Repository
     */
    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }
    
    /**
     * Persists data for the current repository method.
     * 
     * @param mixed $item The item to store.
     * @param mixed $time The time to store the item for.
     * 
     * @return \Habitat\Repository
     */
    protected function persist($item, $time = null)
    {
        return $this->persistFor($this->getLastMethod(), $this->getLastArgs(), $item, $time);
    }
    
    /**
     * Retrieves the item for the current repository method.
     * 
     * @return mixed
     */
    protected function retrieve()
    {
        return $this->retrieveFor($this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Expires the item for the current repository method.
     * 
     * @return \Habitat\Repository
     */
    protected function expire()
    {
        return $this->expireFor($this->getLastMethod(), $this->getLastArgs());
    }
    
    /**
     * Provides a way to cache a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * @param mixed  $item   The item to cache.
     * @param mixed  $time   The time to cache the item for.
     * 
     * @return \Habitat\Repository
     */
    protected function persistFor($method, array $args, $item, $time = null)
    {
        if ($this->cache) {
            $this->cache->set($this->generateCacheKey($method, $args), $item, $time);
        }
        return $this;
    }
    
    /**
     * Provides a way to cache a method other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return \Habitat\Repository
     */
    protected function retrieveFor($method, array $args)
    {
        if ($this->cache) {
            return $this->cache->get($this->generateCacheKey($method, $args));
        }
        return false;
    }
    
    /**
     * Provides a way to expire a method cache other than the one that was called.
     * 
     * @param string $method The method to cache for.
     * @param array  $args   The arguments to cache for.
     * 
     * @return \Habitat\Repository
     */
    protected function expireFor($method, array $args)
    {
        if ($this->cache) {
            $this->cache->remove($this->generateCacheKey($method, $args));
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
        return md5(get_class($this) . $method . serialize($args));
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
        return $callstack[count($callstack) - 3]['function'];
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
        return $callstack[count($callstack) - 3]['args'];
    }
}