<?php

namespace Model\Configurator\DocComment\Entity\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionClass;

class Configure
{
    public function __invoke(DocTagInterface $tag, ReflectionClass $class, Entity $entity)
    {
        $configurator = $tag->getValue();

        if (!class_exists($configurator)) {
            throw new InvalidArgumentException(sprintf(
                'Cannot configure entity "%s" because the configuration class "%s" does not exist.',
                get_class($entity),
                $configurator
            ));
        }

        $configurator = new $configurator;
        $configurator->__invoke($entity);
    }
}