<?php

namespace Provider\Validator;

class Email
{
    public function __invoke($value)
    {
        return preg_match('/[\w\d\+]+@[\w\d]+\.(net|com)/', $value);
    }
}