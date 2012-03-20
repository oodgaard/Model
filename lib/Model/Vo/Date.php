<?php

namespace Model\Vo;
use DateTime;
use DateTimeZone;

class Date implements VoInterface
{
    private $config = array(
        'format'   => 'Y-m-d H:i:s',
        'timezone' => null
    );
    
    private $date;
    
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->config, $config);
    }
    
    public function set($value)
    {
        $this->date = new DateTime($value, new DateTimeZone($this->config['timezone']));
    }
    
    public function get()
    {
        return $this->date->format($this->format);
    }
    
    public function exists()
    {
        return $this->date instanceof DateTime;
    }
    
    public function remove()
    {
        $this->date = null;
    }
}