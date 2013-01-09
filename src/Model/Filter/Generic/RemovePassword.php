<?php

namespace Model\Filter\Generic;

class RemovePassword
{
    const NAME = 'password';

    public function __invoke(\Model\Entity\Entity $data)
    {
        $data->removeVo(self::NAME);

        return $data;
    }
}
