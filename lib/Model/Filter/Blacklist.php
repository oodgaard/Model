<?php

namespace Model\Filter;

class Blacklist implements FilterInterface
{
    /**
     * The blacklisted properties.
     * 
     * @var array
     */
    private $blacklist = array();
    
    /**
     * Sets the blacklisted properties.
     * 
     * @param array $blacklist The properties to blacklist.
     * 
     * @return Blacklist
     */
    public function __construct(array $blacklist)
    {
        $this->blacklist = $blacklist;
    }
    
    /**
     * Returns false if the property should not be set.
     * 
     * @param string $name The property name.
     * 
     * @return bool
     */
    public function filter($name)
    {
        if (isset($this->blacklist[$name])) {
            return false;
        }
    }
}