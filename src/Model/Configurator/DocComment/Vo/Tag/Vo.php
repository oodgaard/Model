<?php

namespace Model\Configurator\DocComment\Vo\Tag;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;

class Vo
{
    public function __invoke(DocTagInterface $tag, ReflectionProperty $property, Entity $entity)
    {
        $parts = preg_split('/\s+/', $tag->getValue(), 2);
        $class = function() use ($parts) {
            if (isset($parts[1])) {
                $class = eval('return new ' . $parts[0] . '(' . $parts[1] . ');');
            } else {
                $class = new $parts[0];
            }
            return $class;
        };
        
        $class = $class->bindTo($entity);
        $class = $class();
        
        $entity->setVo($property->getName(), $class);
        $this->setDefaultValueIfExists($entity, $property);
        
        unset($entity->{$property->getName()});
    }

    private function setDefaultValueIfExists($entity, ReflectionProperty $property)
    {
        $name = $property->getName();
        $prop = $property->getDeclaringClass()->getDefaultProperties();

        if (isset($prop[$name])) {
            $entity->__set($name, $prop[$name]);
        }
    }
}