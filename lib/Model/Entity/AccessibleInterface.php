<?php

namespace Model\Entity;
use ArrayAccess;
use Countable;
use Iterator;
use Serializable;

/**
 * Basic accessible interface defining common interfaces.
 * 
 * @category Accessibility
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface AccessibleInterface extends ArrayAccess, Countable, Iterator, Serializable
{
     /**
      * Fills values from an array.
      * 
      * @param mixed $values The values to import.
      * 
      * @return AccessibleInterface
      */
    public function fill($values);
    
    /**
     * Fills the entity with the specified values.
     * 
     * @return array
     */
    public function toArray();
    
    /**
     * Clears all of the data on the object.
     * 
     * @return array
     */
    public function clean();
    
    /**
     * Returns whether or not the specified accessible is clean.
     * 
     * @return bool
     */
    public function isClean();
    
    /**
     * Validates the accessible item.
     * 
     * @throws ValidatorException If the item is not valid.
     * 
     * @return AccessibleInterface
     */
    public function validate();
}
