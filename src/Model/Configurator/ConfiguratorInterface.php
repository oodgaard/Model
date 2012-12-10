<?php

namespace Model\Configurator;

interface ConfiguratorInterface
{
    public function configure(ConfigurableInterface $entity);
}