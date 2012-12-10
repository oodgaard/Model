<?php

namespace Model\Configurator\DocComment\Entity;
use Model\Configurator\ConfigurableInterface;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Mapper\MapperArray;
use Reflector;

class MapperTag implements DocTagInterface
{
    private static $cache = [];

    public function configure($value, Reflector $reflector, ConfigurableInterface $configurable)
    {
        $parts = explode(' ', $value);
        $name  = array_shift($parts);
        $key   = $reflector->getName() . $name;
        
        if (isset(self::$cache[$key])) {
            $configurable->setMapper($name, self::$cache[$key]);
            return;
        }
        
        $mapArr = new MapperArray;

        foreach ($parts as $class) {
            if ($class = trim($class)) {
                $mapArr->add(new $class);
            }
        }
        
        $configurable->setMapper($name, $mapArr);
        
        self::$cache[$key] = $mapArr;
    }
}