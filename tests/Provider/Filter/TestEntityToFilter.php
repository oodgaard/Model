<?php

namespace Provider\Filter;

class TestEntityToFilter
{
    public function __invoke($entity)
    {
        $entity['testEntityTo'] = true;
    }
}