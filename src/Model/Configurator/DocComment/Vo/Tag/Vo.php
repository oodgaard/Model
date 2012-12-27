<?php

namespace Model\Configurator\DocComment\Vo\Tag;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;

class Vo
{
    private static $classCache = [];

    private static $valueCache = [];

    public function __invoke(DocTagInterface $tag, ReflectionProperty $property, Entity $entity)
    {
        $name     = $property->getName();
        $cacheKey = get_class($entity) . $name . $tag->getValue();

        if (isset(self::$classCache[$cacheKey])) {
            $class = self::$classCache[$cacheKey];
            $value = self::$valueCache[$cacheKey];
        } else {
            $class = self::$classCache[$cacheKey] = $this->generateClass($tag, $entity);
            $value = self::$valueCache[$cacheKey] = $this->generateValue($property);
        }
        
        $entity->setVo($name, $class);

        if ($value) {
            $entity->__set($name, $value);
        }
    }

    private function generateClass(DocTagInterface $tag, Entity $entity)
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

        return $class;
    }

    private function generateValue(ReflectionProperty $property)
    {
        $name = $property->getName();
        $prop = $property->getDeclaringClass()->getDefaultProperties();

        if (isset($prop[$name])) {
            return $prop[$name];
        }
    }
}