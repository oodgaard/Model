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

    public function init()
    {
        return $this->datetime->format($this->config['format']);
    }

    public function translate($value)
    {
        if ($value instanceof DateTime) {
            $this->datetime = $value;
        } elseif (is_numeric($value)) {
            $this->datetime->setTimestamp($value);
        } else {
            $this->datetime->modify($value);
        }

        return $this->init();
    }
}