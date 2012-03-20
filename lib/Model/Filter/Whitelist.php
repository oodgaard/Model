<?php

namespace Model\Filter;

class Whitelist implements FilterInterface
{
    /**
     * The whitelisted properties.
     * 
     * @var array
     */
    private $whitelist;
    
    /**
     * Sets the whitelisted properties.
     * 
     * @param array $$whitelist The properties to whitelist.
     * 
     * @return Blacklist
     */
    public function __construct(array $whitelist)
    {
        $this->whitelist = $whitelist;
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
        if ($this->whitelist && !isset($this->whitelist[$name])) {
            return false;
        }
    }
}