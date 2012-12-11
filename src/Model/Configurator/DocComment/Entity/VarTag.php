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
    public function configure($value, Reflector $reflector, $entity)
    {
        if (!$entity instanceof Entity) {
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
        
        $class = $class->bindTo($entity);
        $class = $class();
        
        $entity->setVo($reflector->getName(), $class);
        $this->setDefaultValueIfExists($entity, $reflector);
        
        unset($entity->{$reflector->getName()});
    }

    private function setDefaultValueIfExists($entity, Reflector $reflector)
    {
        $name = $reflector->getName();
        $prop = $reflector->getDeclaringClass()->getDefaultProperties();

        if (isset($prop[$name])) {
            $entity->__set($name, $prop[$name]);
        }
    }
}