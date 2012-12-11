<?php

namespace Model\Configurator\DocComment\Repository;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Configurator\ConfigurableInterface;
use Model\Entity\Set;
use Reflector;
use ReflectionMethod;

class ReturnTag implements DocTagInterface
{
    private static $cache = [];

    public function configure($value, Reflector $reflector, $repository)
    {
        $method   = $reflector->getName();
        $cacheKey = $reflector->getDeclaringClass()->getName() . $method;

        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = $this->generateFilter($reflector);
        }

        if (is_callable(self::$cache[$cacheKey])) {
            $repository->setReturnValueFilter($method, self::$cache[$cacheKey]);
        }
    }

    private function generateFilter(Reflector $reflector)
    {
        $info = $this->parseAutomatedReturnValue($reflector);
        
        if ($info['set']) {
            return $this->generateFilterForSet($info['entity'], $info['mapper']);
        }

        return $this->generateFilterForEntity($info['entity'], $info['mapper']);
    }

    private function generateFilterForSet($entity, $mapper)
    {
        return function($value) use ($entity, $mapper) {
            return new Set($entity, $value, $mapper);
        };
    }

    private function generateFilterForEntity($entity, $mapper)
    {
        return function($value) use ($entity, $mapper) {
            return $value ? new $entity($value, $mapper) : null;
        };
    }

    private function parseAutomatedReturnValue(ReflectionMethod $reflector)
    {
        $docblock = $reflector->getDocComment();

        if (preg_match('/@return (.+)/', $docblock, $matches)) {
            $return = $matches[1];
        } else {
            $return = '';
        }

        return [
            'set'    => $this->parseAutomatedReturnValueSet($return),
            'entity' => $this->parseAutomatedReturnValueEntity($return),
            'mapper' => $this->parseAutomatedReturnValueMapper($return)
        ];
    }

    private function parseAutomatedReturnValueSet($return)
    {
        if (strpos($return, 'Set of ') !== false) {
            return explode('Set of ', $return)[0];
        }
    }

    private function parseAutomatedReturnValueEntity($return)
    {
        if (strpos($return, 'Set of ') !== false) {
            return $return = explode('Set of ', $return)[1];
        }

        if (strpos($return, ' using ') !== false) {
            return $return = explode(' using ', $return)[0];
        }

        return trim($return);
    }

    private function parseAutomatedReturnValueMapper($return)
    {
        if (strpos($return, ' using ') !== false) {
            return trim(explode(' using ', $return)[1]);
        }
    }
}