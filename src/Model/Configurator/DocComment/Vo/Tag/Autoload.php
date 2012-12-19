<?php

namespace Model\Configurator\DocComment\Vo\Tag;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;

class Autoload
{
    public function __invoke(DocTagInterface $tag, ReflectionProperty $property, Entity $entity)
    {
        $entity->setAutoloader($property->getName(), $tag->getValue());
    }
}