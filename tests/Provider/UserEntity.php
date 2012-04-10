<?php

namespace Provider;
use Model\Entity\Entity;

class UserEntity extends Entity
{
    /**
     * Returns the user's content items.
     * 
     * @var Model\Vo\Proxy array($this, 'proxyGetContent')
     */
    private $content;
    
    /**
     * Returns whether or not this is the last administrator.
     * 
     * @var Model\Vo\Proxy array($this, 'proxyIsLastAdministrator')
     */
    private $isLastAdministrator;
    
    public function proxyGetContent()
    {
        return (new UserRepository)->getContent($this);
    }
    
    public function proxyIsLastAdministrator()
    {
        return (new UserRepository)->isLastAdministrator($this);
    }
}