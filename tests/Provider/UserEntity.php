<?php

namespace Provider;
use Model\Entity;

class UserEntity extends Entity
{
    public function init()
    {
        $this->mapGetter('content', 'getContent');
        $this->mapGetter('isLastAdmin', 'isLastAdmin');
    }
    
    public function getContent()
    {
        $repo = new UserRepository;
        return $repo->getContent($this);
    }
    
    public function isLastAdmin()
    {
        $repo = new UserRepository;
        return $repo->isLastAdministrator($this);
    }
}