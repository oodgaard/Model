<?php

namespace Model\Configurator\DocComment\Entity;
use InvalidArgumentException;
use Model\Configurator\ConfigurableInterface;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionClass;
use ReflectionProperty;
use Reflector;
use RuntimeException;
use Zend\Validator\Validator;
use Zend_Validate_Interface;

class ValidatorTag implements DocTagInterface
{
    private $cache = [];

    public function configure($value, Reflector $reflector, $entity)
    {
        if (!$entity instanceof Entity) {
            throw new InvalidArgumentException('The @validator tag can only be applied to an entity class or entity property.');
        }

        $parts     = explode(' ', $value, 2);
        $validator = $this->resolveValidator($parts[0], $entity);
        $message   = isset($parts[1]) ? $parts[1] : null;
        
        if ($reflector instanceof ReflectionClass) {
            $this->configureClass($reflector, $entity, $message, $validator);
        } elseif ($reflector instanceof ReflectionProperty) {
            $this->configureProperty($reflector, $entity, $message, $validator);
        } else {
            throw new InvalidAgumentException('The @validator tag can only be applied to an entity or entity value object.');
        }
    }

    private function configureClass(ReflectionClass $class, $entity, $message, $validator)
    {
        $entity->addValidator($message ?: $this->getDefaultClassMessage($class), $validator);
    }

    private function configureProperty(ReflectionProperty $property, $entity, $message, $validator)
    {
        $property = $property->getName();
        
        if (!$entity->hasVo($property)) {
            throw new RuntimeException(sprintf(
                'You cannot apply the @valid tag to "%s::$%s" because it has not been given a VO yet.',
                get_class($entity),
                $property
            ));
        }
        
        $entity->getVo($property)->addValidator($message ?: $this->getDefaultPropertyMessage($property), $validator);
    }

    private function resolveValidator($validator, $entity)
    {
        if (isset($this->cache[$validator])) {
            $validator = $this->cache[$validator];
        } elseif (method_exists($entity, $validator)) {
            $validator = [$entity, $validator];
        } elseif (class_exists($validator)) {
            $validator = new $validator;

            if ($validator instanceof Zend_Validate_Interface || $validator instanceof Validator) {
                $validator = function($value) use ($validator) {
                    return $validator->isValid($value);
                };
            }

            $this->cache[get_class($validator)] = $validator;
        } elseif (function_exists($validator)) {
            $this->cache[$validator] = $validator;
        } else {
            throw new RuntimeException(sprintf('Unknown validator "%s" specified for "%s".', $validator, get_class($configurable)));
        }

        return $validator;
    }

    private function getDefaultPropertyMessage(Reflector $reflector)
    {
        return 'Value Object "' . $reflector->getName() . '" is not valid.';
    }

    private function getDefaultClassMessage(Reflector $reflector)
    {
        return 'Entity "' . $reflector->getName() . '" is not valid.';
    }
}