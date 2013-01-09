<?php

namespace Model\Filter\Generic;

class RemoveMongoId
{
    const NAME = '_id';

    public function __invoke(\Model\Entity\Entity $data)
    {
        $data->removeVo(self::NAME);

        return $data;
    }
}
