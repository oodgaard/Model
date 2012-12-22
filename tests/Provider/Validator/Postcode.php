<?php

namespace Provider\Validator;

class Postcode
{
    public function __invoke($value)
    {
        return preg_match('/[a-zA-z0-9]{5,}/', $value);
    }
}