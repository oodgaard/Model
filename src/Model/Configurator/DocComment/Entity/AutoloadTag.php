<?php

namespace Model\Configurator\DocComment\Entity;
use Model\Configurator\ConfigurableInterface;
use Model\Configurator\DocComment\DocTagInterface;
use Reflector;

class AutoloadTag implements DocTagInterface
{
    public function configure($value, Reflector $reflector, $entity)
    {
        $entity->setAutoloader($reflector->getName(), $value);
    }
}