<?php

namespace Model\Filter\From;

class MongoDate
{
    public function __invoke(\MongoDate $date)
    {
        return date('U', $date->sec);
    }
}