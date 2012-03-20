<?php

namespace Model\Vo;

class Alias implements VoInterface
{
    private $vo;
    
    public function __construct(Entity $entity, $name)
    {
        $this->vo = $entity->getVo($name);
    }
    
    public function set($value)
    {
        $this->vo->set($value);
    }
    
    public function get()
    {
        return $this->vo->get();
    }
    
    public function exists()
    {
        return $this->vo->exists();
    }
    
    public function remove()
    {
        return $this->vo->remove();
    }
}