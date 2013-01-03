<?php

namespace Model\Configurator\DocComment\Vo;
use Model\Configurator\DocComment\ConfiguratorAbstract;
use Model\Entity\Entity;
use ReflectionClass;

class Configurator extends ConfiguratorAbstract
{
    public function __construct()
    {
        $this->addTagHandler('auto', new Tag\Autoload);
        $this->addTagHandler('autoload', new Tag\Autoload);
        $this->addTagHandler('configure', new Tag\Configure);
        $this->addTagHandler('filter', new Tag\Filter);
        $this->addTagHandler('valid', new Tag\Validator);
        $this->addTagHandler('validator', new Tag\Validator);
        $this->addTagHandler('vo', new Tag\Vo);
    }

    public function __invoke(Entity $entity)
    {
        $class = new ReflectionClass($entity);

        foreach ($class->getProperties() as $property) {
            if ($property->isPublic()) {
                $this->configure($property, $entity);
            }
        }
    }
}