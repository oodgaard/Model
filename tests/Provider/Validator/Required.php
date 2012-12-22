<?php

namespace Provider\Validator;

class Required
{
    public function __invoke($value)
    {
        return $value === null;
    }
}