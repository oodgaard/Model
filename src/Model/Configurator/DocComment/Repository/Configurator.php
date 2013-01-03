<?php

namespace Model\Configurator\DocComment\Repository;
use Model\Configurator\DocComment\ConfiguratorAbstract;
use Model\Repository\RepositoryAbstract;
use ReflectionClass;

class Configurator extends ConfiguratorAbstract
{
    public function __construct()
    {
        $this->addTagHandler('cache', new Tag\Cache);
        $this->addTagHandler('ensure', new Tag\Ensure);
    }

    public function __invoke(RepositoryAbstract $repository)
    {
        $reflector = new ReflectionClass($repository);

        foreach ($reflector->getMethods() as $method) {
            $this->configure($method, $repository);
        }
    }
}