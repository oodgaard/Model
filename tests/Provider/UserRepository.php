<?php

namespace Provider;
use Model\Entity\Set;

class UserRepository extends BaseRepository
{
    public function getContent()
    {
        return new Set('Provider\ContentEntity', [[
            'id'   => 1,
            'name' => 'Proxy content 1'
        ], [
            'id'   => 2,
            'name' => 'Proxy content 2'
        ]]);
    }
    
    public function isLastAdministrator()
    {
        return true;
    }
}