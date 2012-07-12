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
interface MapperInterface
{
    /**
     * Converts the input array to the output array.
     * 
     * @return array
     */
    public function map(array $from);
}