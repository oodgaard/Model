<?php

namespace Habitat;

/**
 * Basic accessible interface defining common interfaces.
 * 
 * @category Accessibility
 * @package  Habitat
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface Accessible extends \ArrayAccess, \Countable, \Iterator, \Serializable
{
     /**
      * Fills values from an array.
      * 
      * @param mixed $array The values to import.
      * 
      * @return \Habitat\Entity
      */
    public function import($array);
    
    /**
     * Fills the entity with the specified values.
     * 
     * @return \Habitat\Entity
     */
    public function export();
}