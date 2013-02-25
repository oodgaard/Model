<?php

namespace Model\Repository;
use InvalidArgumentException;
use LogicException;
use Model\Cache\CacheInterface;
use Model\Configurator\DocComment\Repository\Configurator;
use ReflectionClass;
use ReflectionMethod;

abstract class RepositoryAbstract
{
    private $cacheDrivers = [];

    private $cacheLifetimes = [];

    private $cacheLinks = [];

    private $returnValueFilters = [];

    public function __construct()
    {
        $this->configure();

        if (method_exists($this, 'init')) {
            if (func_num_args()) {
                call_user_func_array([$this, 'init'], func_get_args());
            } else {
                $this->init();
            }
        }
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

    public function get($service)
    {
        if (isset($this->services[$service])) {
            return $this->services[$service];
        }

        throw new InvalidArgumentException(sprintf('The service "%s" was not injected into "%s".', $service, get_class($this)));
    }

    public function configure()
    {
        $conf = new Configurator;
        $conf->__invoke($this);
    }

    public function setCacheDriver($name, CacheInterface $driver)
    {
        $this->cacheDrivers[$name] = $driver;
        return $this;
    }

    public function getCacheDriver($name)
    {
        if (!isset($this->cacheDrivers[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The cache driver "%s" does not exist for repository "%s".',
                $name,
                get_class($this)
            ));
        }

        return $this->cacheDrivers[$name];
    }

    public function hasCacheDriver($name)
    {
        return isset($this->cacheDrivers[$name]);
    }

    public function removecacheDriver($name)
    {
        if (isset($this->cacheDrivers[$name])) {
            unset($this->cacheDrivers[$name]);
        }

        return $this;
    }

    public function setCacheDriverFor($method, $name, $lifetime = null)
    {
        if (!isset($this->cacheDrivers[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The cannot apply cache to "%s" because repsoitory "%s" does not have a cache driver labelled "%s".',
                $method,
                get_class($this),
                $name
            ));
        }

        $this->methodCacheDrivers[$method]   = $this->cacheDrivers[$name];
        $this->methodCacheLifetimes[$method] = $lifetime;

        return $this;
    }

    public function getCacheDriverFor($method)
    {
        if (!isset($this->methodCacheDrivers[$method])) {
            throw new InvalidArgumentException(sprintf(
                'No cache driver exists for method "%s" in repository "%s".',
                $method,
                get_class($this)
            ));
        }

        return $this->methodCacheDrivers[$method];
    }

    public function removeCacheDriverFor($method)
    {
        if (isset($this->methodCacheDrivers[$method])) {
            unset($this->methodCacheDrivers[$method]);
        }

        return $this;
    }

    public function hasCacheDriverFor($method)
    {
        return isset($this->methodCacheDrivers[$method]);
    }

    public function setCache($method, array $args, $value)
    {
        if ($this->hasCacheDriverFor($method)) {
            return $this->getCacheDriverFor($method)->set(
                $this->generateCacheKey($method, $args),
                $value,
                isset($this->methodCacheLifetimes[$method]) ? $this->methodCacheLifetimes[$method] : null
            );
        }

        return $this;
    }

    public function getCache($method, array $args)
    {
        if ($this->hasCacheDriverFor($method)) {
            return $this->getCacheDriverFor($method)->get($this->generateCacheKey($method, $args));
        }
    }

    public function hasCache($method, array $args)
    {
        if ($this->hasCacheDriverFor($method)) {
            return $this->getCacheDriverFor($method)->has($this->generateCacheKey($method, $args));
        }

        return false;
    }

    public function removeCache($method, array $args)
    {
        if ($this->hasCacheDriverFor($method)) {
            $this->getCacheDriverFor($method)->remove($this->generateCacheKey($method, $args));
        }

        return $this;
    }

    public function clearCache()
    {
        foreach ($this->methodCacheDrivers as $method => $driver) {
            $this->getCacheDriverFor($method)->clear();
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
        return md5(get_class($this) . $method . serialize($args));
    }

    private function filterReturnValue($method, $value)
    {
        if (isset($this->returnValueFilters[$method])) {
            $value = $this->returnValueFilters[$method]($value);
        }

        return $value;
    }

    private function throwIfMethodNotExists($method)
    {
        if (!method_exists($this, $method)) {
            $class = get_class($this);
            $trace = debug_backtrace();

            foreach ($trace as $k => $call) {
                if ($call['class'] === $class && $call['function'] === $method) {
                    $origin = $trace[$k + 1];
                    $origin['file'] = $call['file'];
                    $origin['line'] = $call['line'];
                    break;
                }
            }

            throw new LogicException(sprintf(
                'The method "%s" does not exist in "%s" as called from "%s%s%s() in "%s" on line "%s".',
                $method,
                $class,
                $origin['class'],
                $origin['type'],
                $origin['function'],
                $origin['file'],
                $origin['line']
            ));
        }
    }

    private function throwIfMethodNotProtected($method)
    {
        $reflector = new ReflectionMethod($this, $method);

        if (!$reflector->isProtected()) {
            throw new LogicException(sprintf(
                'You must define "%s::%s()" as protected.',
                get_class($this),
                $method
            ));
        }
    }
}