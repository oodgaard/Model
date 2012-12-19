<?php

namespace Model\Configurator\DocComment\Vo\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionProperty;
use Zend\Validator\Validator as Zend2xValidator;
use Zend_Validate_Interface as Zend1xValidator;

class Validator
{
    private $cache = [];

    public function __invoke(DocTagInterface $tag, ReflectionProperty $property, Entity $entity)
    {
        $parts     = explode(' ', $tag->getValue(), 2);
        $validator = $this->resolveValidator($parts[0], $entity);
        $message   = isset($parts[1]) ? $parts[1] : null;
        $message   = $message ?: 'Value Object "' . $property->getName() . '" is not valid.';
        
        if (!$entity->hasVo($property->getName())) {
            throw new InvalidArgumentException(sprintf(
                'You cannot apply the @validator tag to "%s::$%s" because it has not been given a VO yet.',
                get_class($entity),
                $property->getName()
            ));
        }
        
        $entity->getVo($property->getName())->addValidator($message, $validator);
    }

    private function resolveValidator($validator, $entity)
    {
        if (isset($this->cache[$validator])) {
            $validator = $this->cache[$validator];
        } elseif (method_exists($entity, $validator)) {
            $validator = [$entity, $validator];
        } elseif (class_exists($validator)) {
            $validator = new $validator;

            if ($validator instanceof Zend1xValidator || $validator instanceof Zend2xValidator) {
                $validator = function($value) use ($validator) {
                    return $validator->isValid($value);
                };
            }

            $this->cache[get_class($validator)] = $validator;
        } elseif (function_exists($validator)) {
            $this->cache[$validator] = $validator;
        } else {
            throw new InvalidArgumentException(sprintf('Unknown validator "%s" specified for "%s".', $validator, get_class($configurable)));
        }

        return $validator;
    }
}