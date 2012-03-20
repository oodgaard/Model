<?php

namespace Model\Filter;

interface FilterInterface
{
    /**
     * Returns false if the property should not be set.
     * 
     * @param string $name The property name.
     * 
     * @return bool
     */
    public function filter($name);
}