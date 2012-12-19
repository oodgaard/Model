<?php

namespace Model\Entity;
use ArrayAccess;
use Countable;
use Iterator;
use Model\Filter\FilterableInterface;
use Model\Validator\ValidatableInterface;
use Serializable;

/**
 * Basic accessible interface defining common interfaces.
 * 
 * @category Accessibility
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface AccessibleInterface extends ArrayAccess, Countable, FilterableInterface, Iterator, Serializable
{
    /**
     * Clears all values on the object.
     * 
     * @return AccessibleInterface
     */
    public function clear();
    
    /**
     * Fills values from an array.
     * 
     * @param mixed  $data   The values to import.
     * @param string $mapper The mapper to use to export the data.
     * 
     * @return AccessibleInterface
     */
    public function fill($data, $mapper = null);
    
    /**
     * Fills the entity with the specified values.
     * 
     * @param string $mapper A mapper to use for importing data.
     * 
     * @return array
     */
    public function toArray($mapper = null);
}