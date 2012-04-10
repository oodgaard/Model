<?php

namespace Model\Vo;
use DateTime;
use DateTimeZone;

/**
 * Date / Time VO.
 * 
 * @category ValueObjects
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
class Date implements VoInterface
{
    /**
     * The configuration for the date.
     * 
     * @var array
     */
    private $config = array(
        'format'   => 'Y-m-d\TH:i:s\Z',
        'timezone' => null
    );
    
    /**
     * The date.
     * 
     * @var DateTime
     */
    private $date;
    
    /**
     * Sets up a new date VO.
     * 
     * @param array $config The date configuration.
     * 
     * @return Date
     */
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->config, $config);
        
        if ($this->config['timezone']) {
            $this->date = new DateTime('now', new DateTimeZone($this->config['timezone']));
        } else {
            $this->date = new DateTime('now');
        }
    }
    
    /**
     * Initializes the value.
     * 
     * @return void
     */
    public function init()
    {
        $this->date->modify('now');
    }
    
    /**
     * Sets the value.
     * 
     * @param mixed $value The value to set.
     * 
     * @return void
     */
    public function set($value)
    {
        if (is_numeric($value)) {
            $value = date($this->config['format'], $value);
        }
        $this->date->modify($value);
    }
    
    /**
     * Returns the value.
     * 
     * @return mixed
     */
    public function get()
    {
        return $this->date->format($this->config['format']);
    }
}