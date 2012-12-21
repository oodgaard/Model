<?php

namespace Model\Entity;
use ArrayAccess;
use Countable;
use Iterator;
use Serializable;

interface AccessibleInterface extends ArrayAccess, Countable, Iterator, Serializable
{
    public function clear();

    public function fill($data, $mapper = null);

    public function toArray($mapper = null);

    public function from($data, $filter = null);

    public function to($filter = null);

    public function validate();
}