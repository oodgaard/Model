<?php

namespace Model\Configurator\DocComment\Repository\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Set;
use Model\Repository\RepositoryAbstract;
use ReflectionMethod;

class Ensure
{
    private static $cache = [];

    public function __invoke(DocTagInterface $tag, ReflectionMethod $method, RepositoryAbstract $repository)
    {
        $cacheKey = $method->getDeclaringClass()->getName() . $method->getName();

        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = $this->generateFilter($tag->getValue());
        }

        if (is_callable(self::$cache[$cacheKey])) {
            $repository->setReturnValueFilter($method->getName(), self::$cache[$cacheKey]);
        }
    }

    private function generateFilter($tag)
    {
        $info = $this->parseAutomatedReturnValue($tag);

        if (!class_exists($info['entity'])) {
            throw new InvalidArgumentException(sprintf(
                'The entity "%s" could not be found while applying the @ensure annotation for "%s".',
                $info['entity'],
                $tag
            ));
        }
        
        if ($info['set']) {
            return $this->generateFilterForSet($info['entity'], $info['filter']);
        }

        return $this->generateFilterForEntity($info['entity'], $info['filter']);
    }

    private function generateFilterForSet($entity, $filter)
    {
        return function($value) use ($entity, $filter) {
            return new Set($entity, $value, $filter);
        };
    }

    private function generateFilterForEntity($entity, $filter)
    {
        return function($value) use ($entity, $filter) {
            return $value ? new $entity($value, $filter) : null;
        };
    }

    private function parseAutomatedReturnValue($tag)
    {
        return [
            'set'    => $this->parseAutomatedReturnValueSet($tag),
            'entity' => $this->parseAutomatedReturnValueEntity($tag),
            'filter' => $this->parseAutomatedReturnValueFilter($tag)
        ];
    }

    private function parseAutomatedReturnValueSet($ensure)
    {
        return strpos($ensure, 'Set of ') !== false;
    }

    private function parseAutomatedReturnValueEntity($ensure)
    {
        if (strpos($ensure, 'Set of ') !== false) {
            $ensure = explode('Set of ', $ensure)[1];
        }

        $ensure = explode(' ', $ensure)[0];

        return trim($ensure);
    }

    private function parseAutomatedReturnValueFilter($ensure)
    {
        if (strpos($ensure, ' using ') !== false) {
            return trim(explode(' using ', $ensure)[1]);
        }
    }
}