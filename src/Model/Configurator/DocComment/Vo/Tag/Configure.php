<?php

namespace Model\Configurator\DocComment\Vo\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;

class Configure
{
    public function __invoke(DocTagInterface $tag, ReflectionProperty $property, Entity $entity)
    {
        $configurator = $tag->getValue();

        if (!class_exists($configurator)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot configure property "%s" for entity "%s" because the configuration class "%s" does not exist.',
                $property->getName(),
                get_class($entity),
                $configurator
            ));
        }

        $configurator = new $configurator;
        $configurator->__invoke($entity);
    }
}