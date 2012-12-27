<?php

namespace Model\Configurator\DocComment\Entity;
use Model\Configurator\DocComment\ConfiguratorAbstract;
use Model\Entity\Entity;
use ReflectionClass;

class Configurator extends ConfiguratorAbstract
{
    public function __construct()
    {
        $this->addTagHandler('configure', new Tag\Configure);
        $this->addTagHandler('filter', new Tag\Filter);
        $this->addTagHandler('map', new Tag\Mapper);
        $this->addTagHandler('mapper', new Tag\Mapper);
        $this->addTagHandler('valid', new Tag\Validator);
        $this->addTagHandler('validator', new Tag\Validator);
    }

    public function __invoke(Entity $entity)
    {
        $this->configure(new ReflectionClass($entity), $entity);
    }
}