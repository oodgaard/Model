<?php

namespace Model\Entity;
use InvalidArgumentException;
use Model\Configurator\DocComment\Entity\Configurator as EntityConfigurator;
use Model\Configurator\DocComment\Vo\Configurator as VoConfigurator;
use Model\Filter\Filterable;
use Model\Filter\FilterableInterface;
use Model\Mapper\MapperInterface;
use Model\Validator\Assertable;
use Model\Validator\AssertableInterface;
use Model\Vo\VoInterface;

class Entity implements AccessibleInterface, AssertableInterface
{
    use Assertable;
    
    use Filterable;
    
    private $autoloaders = [];

    private $data = [];

    private $mappers = [];

    private static $cache = [];

    private static $cacheProperties = [
        'autoloaders',
        'exportFilters',
        'importFilters',
        'mappers',
        'validatorMessages',
        'validators'
    ];
    
    public function __construct($data = [], $filterToUse = null)
    {
        $this->configure();
        $this->init();
        $this->from($data, $filterToUse);
    }
    
    public function __set($name, $value)
    {
        if (isset($this->data[$name])) {
            $this->data[$name]->set($value);
        }
    }
    
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            if (isset($this->autoloaders[$name]) && !$this->data[$name]->exists()) {
                $this->data[$name]->set($this->{$this->autoloaders[$name]}());
            }
            return $this->data[$name]->get();
        }
    }
    
    public function __isset($name)
    {
        return isset($this->data[$name]) && $this->data[$name]->exists();
    }
    
    public function __unset($name)
    {
        if (isset($this->data[$name])) {
            $this->data[$name]->remove();
        }
    }
    
    public function init()
    {
        
    }
    
    public function clear()
    {
        foreach ($this->data as $vo) {
            $vo->remove();
        }

        return $this;
    }
    
    public function setVo($name, VoInterface $vo)
    {
        $this->data[$name] = $vo;

        return $this;
    }
    
    public function getVo($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        throw new InvalidArgumentException(sprintf('The VO "%s" does not exist.', $name));
    }
    
    public function hasVo($name)
    {
        return isset($this->data[$name]);
    }
    
    public function removeVo($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }

        return $this;
    }
    
    /**
     * @deprecated
     */
    public function setMapper($name, MapperInterface $mapper)
    {
        $this->mappers[$name] = $mapper;
        return $this;
    }
    
    /**
     * @deprecated
     */
    public function getMapper($name)
    {
        if (!isset($this->mappers[$name])) {
            throw new InvalidArgumentException(sprintf('The mapper "%s" does not exist.', $name));
        }
        return $this->mappers[$name];
    }
    
    /**
     * @deprecated
     */
    public function hasMapper($name)
    {
        return isset($this->mappers[$name]);
    }
    
    /**
     * @deprecated
     */
    public function removeMapper($name)
    {
        if (isset($this->mappers[$name])) {
            unset($this->mappers[$name]);
        }

        return $this;
    }
    
    public function setAutoloader($name, $method)
    {
        if (!method_exists($this, $method)) {
            throw new InvalidArgumentException(sprintf(
                'The autoload method "%s" does not exist on "%s".',
                $method,
                get_class($this)
            ));
        }
        
        $this->autoloaders[$name] = $method;
        
        return $this;
    }
    
    public function getAutoloader($name)
    {
        if (!isset($this->autoloaders[$name])) {
            throw new InvalidArgumentException(sprintf('The autoloader "%s" does not exist.', $name));
        }

        return $this->autoloaders[$name];
    }
    
    public function hasAutoloader($name)
    {
        return isset($this->autoloaders[$name]);
    }
    
    public function removeAutoloader($name)
    {
        if (isset($this->autoloaders[$name])) {
            unset($this->autoloaders[$name]);
        }

        return $this;
    }

    public function from($data, $filterToUse = null)
    {
        $data = $this->makeArrayFromAnything($data);

        foreach ($this->getImportFilters()->offsetGet($filterToUse) as $filter) {
            $data = $filter($data);
        }

        foreach ($data as $name => $value) {
            if ($this->hasVo($name)) {
                $this->getVo($name)->from($value, $filterToUse);
            }
        }

        return $this;
    }

    public function to($filterToUse = null)
    {
        $data = [];

        foreach ($this->data as $name => $vo) {
            $data[$name] = $vo->to($filterToUse);
        }

        foreach ($this->getExportFilters()->offsetGet($filterToUse) as $filter) {
            $data = $filter($data);
        }

        return $data;
    }
    
    /**
     * @deprecated
     */
    public function fill($data, $mapper = null)
    {
        if (!$data) {
            return $this;
        }
        
        if ($mapper && isset($this->mappers[$mapper])) {
            $data = $this->makeArrayFromAnything($data);
            $data = $this->mappers[$mapper]->map($data);
        }
        
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $this->__set($k, $v);
            }
        }
        
        return $this;
    }
    
    /**
     * @deprecated
     */
    public function toArray($mapper = null)
    {
        $array = array();
        
        foreach ($this->data as $k => $v) {
            $v = $this->__get($k);
            
            if ($v instanceof AccessibleInterface) {
                $v = $v->toArray($mapper);
            }
            
            $array[$k] = $v;
        }
        
        if ($mapper && isset($this->mappers[$mapper])) {
            $array = $this->mappers[$mapper]->map($array);
        }
        
        return $array;
    }
    
    public function validate()
    {
        $messages = [];
        
        foreach ($this->data as $vo) {
            if ($voMessages = $vo->validate()) {
                $messages = array_merge($messages, $voMessages);
            }
        }
        
        foreach ($this->validators as $message => $validator) {
            if ($validator($this) === false) {
                $messages[] = $this->validatorMessages[$message];
            }
        }
        
        foreach ($messages as &$message) {
            foreach ($this->data as $name => $vo) {
                if (is_scalar($vo = $vo->get())) {
                    $message = str_replace(':' . $name, $vo, $message);
                }
            }
        }
        
        return $messages;
    }
    
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }
    
    public function offsetGet($name)
    {
        return $this->__get($name);
    }
    
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }
    
    public function offsetUnset($name)
    {
        $this->__unset($name);
    }
    
    public function count()
    {
       return count($this->data); 
    }
    
    public function current()
    {
        return current($this->data)->get();
    }
    
    public function key()
    {
        return key($this->data);
    }
    
    public function next()
    {
        next($this->data);
    }
    
    public function rewind()
    {
        reset($this->data);
    }
    
    public function valid()
    {
        return $this->key() !== null;
    }
    
    public function serialize()
    {
        return serialize([
            'autoloaders'       => $this->autoloaders,
            'data'              => $this->to(),
            'mappers'           => $this->mappers,
            'exportFilters'     => $this->exportFilters,
            'importFilters'     => $this->importFilters,
            'validatorMessages' => $this->validatorMessages,
            'validators'        => $this->validators
        ]);
    }
    
    public function unserialize($data)
    {
        $this->autoloaders       = $data['autoloaders'];
        $this->mappers           = $data['mappers'];
        $this->exportFilters     = $data['exportFilters'];
        $data->importFilters     = $data['importFilters'];
        $this->validatorMessages = $data['validatorMessages'];
        $this->validators        = $data['validators'];

        $this->from($data['data']);
    }
    
    private function makeArrayFromAnything($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            $arr = [];

            foreach ($data as $k => $v) {
                $arr[$k] = $v;
            }

            return $arr;
        }
        
        return [];
    }

    private function configure()
    {
        if ($this->hasCache()) {
            $this->applyCache();
        } else {
            $this->configureUsingAnnotations();
            $this->generateCache();
        }

        $this->removePublicProperties();
    }

    private function configureUsingAnnotations()
    {
        $conf = new EntityConfigurator;
        $conf->__invoke($this);

        $conf = new VoConfigurator;
        $conf->__invoke($this);
    }

    private function removePublicProperties()
    {
        foreach ($this->data as $name => $vo) {
            unset($this->$name);
        }
    }

    private function hasCache()
    {
        $cacheKey = $this->generateCacheKey();
        return isset(self::$cache[$cacheKey]);
    }

    private function applyCache()
    {
        $cacheKey = $this->generateCacheKey();

        foreach (self::$cacheProperties as $name) {
            $this->$name = self::$cache[$cacheKey][$name];
        }

        foreach (self::$cache[$cacheKey]['data'] as $name => $vo) {
            $this->data[$name] = clone $vo;
        }
    }

    private function generateCache()
    {
        $cacheKey = $this->generateCacheKey();

        foreach (self::$cacheProperties as $name) {
            self::$cache[$cacheKey][$name] = $this->$name;
        }

        self::$cache[$cacheKey]['data'] = [];

        foreach ($this->data as $name => $vo) {
            self::$cache[$cacheKey]['data'][$name] = clone $vo;
        }
    }

    private function generateCacheKey()
    {
        return get_class($this);
    }
}