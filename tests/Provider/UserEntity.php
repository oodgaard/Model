<?php

namespace Provider;
use Model\Entity;

class UserEntity extends Entity
{
    public function init()
    {
        $this->hasMany('content', '\Provider\ContentEntity');
        $this->proxy('content', function(UserEntity $user) {
            $repo = new UserRepository;
            return $repo->getContent($user);
        });
        $this->proxy('isLastAdmin', function(UserEntity $user) {
            $repo = new UserRepository;
            return $repo->isLastAdministrator($user);
        });
    }
}