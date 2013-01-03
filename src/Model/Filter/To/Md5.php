<?php

namespace Model\Filter\To;

class Md5
{
    public function __invoke($value)
    {
        return md5($value);
    }
}