<?php

namespace Model\Behavior;
use Model\Entity\Entity;

interface BehaviorInterface
{
    /**
     * Sets up the specified entity according to the behavior definition.
     * 
     * @param Entity $entity The entity to set up.
     * 
     * @return void
     */
    public function behave(Entity $entity);
}