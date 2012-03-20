<?php

namespace Model\Vo;

interface VoInterface
{
    public function set($value);
    
    public function get();
    
    public function exists();
    
    public function remove();
}