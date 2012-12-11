<?php

namespace Model\Entity;
use Model\Behavior\BehaviorInterface;
use Model\Configurator\DocComment;
use Model\Configurator\DocComment\Entity\AutoloadTag;
use Model\Configurator\DocComment\Entity\MapperTag;
use Model\Configurator\DocComment\Entity\ValidatorTag;
use Model\Configurator\DocComment\Entity\VarTag;
use Model\Mapper\MapperInterface;
use Model\Repository;
use Model\Validator\Assertable;
use Model\Validator\AssertableInterface;
use Model\Vo\Generic;
use Model\Vo\VoInterface;
use RuntimeException;

class Entity implements AccessibleInterface, AssertableInterface
{
    use Assertable;
    
    private $autoloaders = [];
    
    private $data = [];
    
    private $mappers = [];
    
    public function __construct($data = [], $mapper = null)
    {
        $this->configure();
        $this->init();
        $this->fill($data, $mapper);
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
    
    public function configure()
    {
        $conf = new DocComment;
        $conf->set('autoload', new AutoloadTag);
        $conf->set('mapper', new MapperTag);
        $conf->set('validator', new ValidatorTag);
        $conf->set('var', new VarTag);
        $conf->configure($this);
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
        if (!isset($this->data[$name])) {
            throw new RuntimeException(sprintf('The VO "%s" does not exist.', $name));
        }
        return $this->data[$name];
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
    
    public function setMapper($name, MapperInterface $mapper)
    {
        $this->mappers[$name] = $mapper;
        return $this;
    }
    
    public function getMapper($name)
    {
        if (!isset($this->mappers[$name])) {
            throw new RuntimeException(sprintf('The mapper "%s" does not exist.', $name));
        }
        return $this->mappers[$name];
    }
    
    public function hasMapper($name)
    {
        return isset($this->mappers[$name]);
    }
    
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
            throw new RuntimeException(sprintf(
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
            throw new RuntimeException(sprintf('The autoloader "%s" does not exist.', $name));
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
    
    public function fill($data, $mapper = null)
    {
        // if there is no data don't do anything
        if (!$data) {
            return $this;
        }
        
        // if there is a mapper we must work some magic
        if ($mapper && isset($this->mappers[$mapper])) {
            $data = $this->makeDataArrayFromAnything($data);
            $data = $this->mappers[$mapper]->map($data);
        }
        
        // let the VOs worry about the value
        if (is_array($data) || is_object($data)) {
            foreach ($data as $k => $v) {
                $this->__set($k, $v);
            }
        }
        
        return $this;
    }
    
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
            'autoloaders' => $this->autoloaders,
            'data'        => $this->toArray(),
            'mappers'     => $this->mappers,
            'validators'  => $this->validators
        ]);
    }
    
    public function unserialize($data)
    {
        $this->autoloaders = $data['autoloaders'];
        $this->mappers     = $data['mappers'];
        $this->validators  = $data['validators'];
        $this->fill($data['data']);
    }
    
    private function makeDataArrayFromAnything($data)
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
}