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
     * Removes all data from the object.
     * 
     * @return AccessibleInterface
     */
    public function init();
    
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
     * Validates the accessible item.
     * 
     * @throws ValidatorException If the item is not valid.
     * 
     * @return AccessibleInterface
     */
    public function validate();
}
