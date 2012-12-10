<?php

namespace Model\Repository;
use LogicException;
use Model\Cache\CacheInterface;
use Model\Configurator\ConfigurableInterface;
use Model\Configurator\DocComment;
use Model\Configurator\DocComment\Repository\ReturnTag;
use ReflectionMethod;

/**
 * Acts as an aggregate for all repository functionality.
 * 
 * @category Repositories
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
abstract class RepositoryAbstract implements ConfigurableInterface
{
    private $cacheDrivers = [];

    private $cacheLifetimes = [];

    private $returnValueFilters = [];

    private static $instances = [];

    public function __construct()
    {
        $this->configure();
        $this->init();
    }

    public function __call($name, array $args = [])
    {
        $this->throwIfMethodNotExists($name);
        $this->throwIfMethodNotProtected($name);

        if ($this->hasCache($name, $args)) {
            return $this->getCache($name, $args);
        }

        $value = call_user_func_array([$this, $name], $args);
        $value = $this->filterReturnValue($name, $value);

        $this->setCache($name, $args, $value);

        return $value;
    }

    public function configure()
    {
        $conf = new DocComment;
        $conf->set('return', new ReturnTag);
        $conf->configure($this);
    }

    public function init()
    {

    }

    public function setCacheDriver($method, CacheInterface $driver, $lifetime = null)
    {
        $this->cacheDrivers[$method]   = $driver;
        $this->cacheLifetimes[$method] = $lifetime;
        return $this;
    }

    public function getCacheDriver($method)
    {
        if (isset($this->cacheDrivers[$method])) {
            return $this->cacheDrivers[$method];
        }
    }

    public function setCache($method, array $args, $value)
    {
        if ($driver = $this->getCacheDriver($method)) {
            return $driver->set(
                $this->generateCacheKey($method, $args),
                $value,
                isset($this->cacheLifetimes[$method]) ? $this->cacheLifetimes[$method] : null
            );
        }
        return $this;
    }

    public function getCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            return $driver->get($this->generateCacheKey($method, $args));
        }
        return false;
    }

    public function hasCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            return $driver->has($this->generateCacheKey($method, $args));
        }
        return false;
    }

    public function removeCache($method, array $args)
    {
        if ($driver = $this->getCacheDriver($method)) {
            $driver->remove($this->generateCacheKey($method, $args));
        }
        return $this;
    }

    public function clearCache()
    {
        foreach ($this->cacheDrivers as $driver) {
            $driver->clear();
        }
        return $this;
    }

    public function setReturnValueFilter($method, callable $filter)
    {
        $this->returnValueFilters[$method] = $filter;
        return $this;
    }

    private function generateCacheKey($method, array $args)
    {
        return md5(get_class() . $method . serialize($args));
    }

    private function filterReturnvalue($method, $value)
    {
        if (isset($this->returnValueFilters[$method])) {
            $value = $this->returnValueFilters[$method]($value);
        }

        return $value;
    }

    private function throwIfMethodNotExists($method)
    {
        if (!method_exists($this, $method)) {
            throw new LogicException(sprintf(
                'The method "%s" does not exist in "%s".',
                $method,
                get_class($this)
            ));
        }
    }

    private function throwIfMethodNotProtected($method)
    {
        $reflector = new ReflectionMethod($this, $method);

        if (!$reflector->isProtected()) {
            throw new LogicException(sprintf(
                'In order to automate the caching of "%s->%s()", you must mark it as protected.',
                get_class($this),
                $method
            ));
        }
    }

    static public function __callStatic($name, array $args = [])
    {
        $self = get_called_class();

        if (!isset(self::$instances[$self])) {
            self::setup();
        }

        if (method_exists(self::$instances[$self], '__call')) {
            return self::$instances[$self]->__call($name, $args);
        }

        return call_user_func_array([self::$instances[$self], $name], $args);
    }

    static public function setup()
    {
        $self = get_called_class();
        
        if (func_num_args()) {
            $classReflector = new ReflectionClass($self);
            self::$instances[$self] = $classReflector->newInstanceArgs(func_get_args());
        } else {
            self::$instances[$self] = new static;
        }
    }
}