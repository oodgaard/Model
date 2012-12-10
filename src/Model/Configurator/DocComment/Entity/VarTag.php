<?php

namespace Model\Configurator\DocComment\Entity;
use Model\Configurator\ConfigurableInterface;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;
use Reflector;
use InvalidArgumentException;

class VarTag implements DocTagInterface
{
    public function configure($value, Reflector $reflector, ConfigurableInterface $configurable)
    {
        if (!$configurable instanceof Entity) {
            throw new InvalidArgumentException('The @var tag can only be applied to an entity.');
        }

        if (!$reflector instanceof ReflectionProperty) {
            throw new InvalidArgumentException('The @var tag can only be applied to public entity properties.');
        }

        if (!$reflector->isPublic()) {
            return;
        }
        
        $value = trim($value);
        $parts = preg_split('/\s+/', $value, 2);
        $class = function() use ($parts) {
            if (isset($parts[1])) {
                $class = eval('return new ' . $parts[0] . '(' . $parts[1] . ');');
            } else {
                $class = new $parts[0];
            }
            return $class;
        };
        
        $class = $class->bindTo($configurable);
        $class = $class();
        
        $configurable->setVo($reflector->getName(), $class);
        $this->setDefaultValueIfExists($configurable, $reflector);
        
        unset($configurable->{$reflector->getName()});
    }

    private function setDefaultValueIfExists(ConfigurableInterface $configurable, Reflector $reflector)
    {
        $name = $reflector->getName();
        $prop = $reflector->getDeclaringClass()->getDefaultProperties();

        if (isset($prop[$name])) {
            $configurable->__set($name, $prop[$name]);
        }
    }
}