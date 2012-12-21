<?php

namespace Model\Vo;
use DateTime;
use DateTimeZone;

class Date extends VoAbstract
{
    private $config = array(
        'format'   => 'Y-m-d\TH:i:s\Z',
        'timezone' => 'UTC'
    );

    private $datetime;

    public function __construct(array $config = [])
    {
        $this->config   = array_merge($this->config, $config);
        $this->datetime = new DateTime('now', new DateTimeZone($this->config['timezone'])); 
    }

    public function translate($value)
    {
        return $this->date->modify($value)->format($this->config['format']);
    }
}