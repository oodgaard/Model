<?php

namespace Model\Filter\Generic;

class RemoveMongoId
{
    const ID = '_id';

    public function __invoke(array $data)
    {
        if (isset($data[self::ID])) {
            unset($data[self::ID]);
        }

        return $data;
    }
}