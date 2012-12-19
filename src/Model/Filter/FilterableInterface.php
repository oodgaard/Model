<?php

namespace Model\Filter;

interface FilterableInterface
{
    public function from($value, $filter);

    public function to($filter);
}