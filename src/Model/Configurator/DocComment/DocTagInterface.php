<?php

namespace Model\Configurator\DocComment;
use Model\Configurator\ConfigurableInterface;
use Reflector;

interface DocTagInterface
{
    public function configure($value, Reflector $reflector, $entityOrRepository);
}