<?php

namespace Provider;
use Model\Entity\Entity;

class UserEntity extends Entity
{
    /**
     * Returns whether or not this is the last administrator.
     * 
     * @autoload autoloadIsLastAdministrator
     * 
     * @vo Model\Vo\Boolean
     */
    public $isLastAdministrator;
    
    public function getContent()
    {
        return (new UserRepository)->getContent();
    }
    
    public function autoloadIsLastAdministrator()
    {
        return (new UserRepository)->isLastAdministrator();
    }
}