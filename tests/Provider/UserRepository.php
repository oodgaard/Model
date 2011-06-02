<?php

namespace Provider;
use Model\EntitySet;
use Model\Repository;

class UserRepository extends BaseRepository
{
    public function getContent(UserEntity $user)
    {
        return new EntitySet('\Provider\UserEntity', array(
            array(
                'id'   => 1,
                'name' => 'Proxy content 1'
            ),
            array(
                'id'   => 2,
                'name' => 'Proxy content 2'
            )
        ));
    }
    
    public function isLastAdministrator(UserEntity $user)
    {
        return true;
    }
}