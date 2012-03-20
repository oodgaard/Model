<?php

namespace Model\Vo;

class Integer implements VoInterface
{
    private $value = null;
    
    public function set($value)
    {
        $this->value = (int) $value;
    }
    
    public function get()
    {
        return $this->value;
    }
    
    public function exists()
    {
        return $this->value !== null;
    }
    
    public function remove()
    {
        $this->value = null;
    }
}