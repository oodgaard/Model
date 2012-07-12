<?php

namespace Model\Mapper;

/**
 * The mapping class that maps one array or object to an array.
 * 
 * @category Mapping
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class MapperArray implements MapperInterface
{
    /**
     * The list of mappers to execute in succession.
     * 
     * @var array
     */
    private $mappers = [];
    
    /**
     * Adds a mapper to the array.
     * 
     * @return MapperArray
     */
    public function add(Mapper $mapper)
    {
        $this->mappers[] = $mapper;
        return $this;
    }
    
    /**
     * Converts the input array to the output array.
     * 
     * @return array
     */
    public function map(array $from)
    {
        foreach ($this->mappers as $mapper) {
            $from = $mapper->map($from);
        }
        return $from;
    }
}