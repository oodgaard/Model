<?php

namespace Model\Filter\To;

class MongoDate
{
    public function __invoke($date)
    {
        if (is_string($date)) {
            $date = strtotime($date);
        }
        
        return new \MongoDate($date);
    }
}