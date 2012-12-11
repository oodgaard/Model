<?php

namespace Model\Configurator;

interface ConfiguratorInterface
{
    public function configure($entityOrRepository);
}