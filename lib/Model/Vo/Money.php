<?php

namespace Model\Vo;

class Money implements VoInterface
{
    private $value;
    
    public function set($value)
    {
        $this->value = number_format($value, 2);
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