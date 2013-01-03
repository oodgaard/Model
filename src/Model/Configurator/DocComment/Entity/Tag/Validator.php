<?php

namespace Model\Configurator\DocComment\Entity\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionClass;
use Zend\Validator\Validator as Zend2xValidator;
use Zend_Validate_Interface as Zend1xValidator;

class Validator
{
    private $cache = [];

    public function __invoke(DocTagInterface $tag, ReflectionClass $class, Entity $entity)
    {
        $parts     = explode(' ', $tag->getValue(), 2);
        $validator = $this->resolveValidator($parts[0], $entity);
        $message   = isset($parts[1]) ? $parts[1] : null;
        $message   = $message ?: 'Entity "' . $class->getName() . '" is not valid.';

        $entity->addValidator($message, $validator);
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