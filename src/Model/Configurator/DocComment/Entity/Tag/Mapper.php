<?php

namespace Model\Configurator\DocComment\Entity\Tag;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Mapper\MapperArray;
use Model\Entity\Entity;
use ReflectionClass;

class Mapper
{
    private static $cache = [];

    public function __invoke(DocTagInterface $tag, ReflectionClass $class, Entity $entity)
    {
        $parts = explode(' ', $tag->getValue());
        $name  = array_shift($parts);
        $key   = $class->getName() . $name;
        
        if (isset(self::$cache[$key])) {
            $entity->setMapper($name, self::$cache[$key]);
            return;
        }
        
        $mapArr = new MapperArray;

        foreach ($parts as $class) {
            if ($class = trim($class)) {
                $mapArr->add(new $class);
            }
        }
        
        $entity->setMapper($name, $mapArr);
        
        self::$cache[$key] = $mapArr;
    }
}