<?php

namespace Model\Configurator\DocComment\Repository\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Repository\RepositoryAbstract;
use ReflectionMethod;

class Cache
{
    private static $cache = [];

    public function __invoke(DocTagInterface $tag, ReflectionMethod $method, RepositoryAbstract $repository)
    {
        $cacheKey = $method->getDeclaringClass()->getName() . $method->getName() . $value;

        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = $this->generateCacheDriverInfo($tag->getValue());
        }

        if (!self::$cache[$cacheKey]['driver']) {
            return;
        }

        if (!$repository->hasCacheDriver(self::$cache[$cacheKey]['driver'])) {
            throw new InvalidArgumentException(sprintf(
                'Cannot apply cache driver "%s" to method "%s" in repository "%s" using the doc tag "%s" because that cache driver does not exist on the repository.',
                self::$cache[$cacheKey]['driver'],
                $method->getName(),
                get_class($repository),
                $tag
            ));
        }
        
        $repository->setCacheDriverFor(
            $method->getName(),
            self::$cache[$cacheKey]['driver'],
            self::$cache[$cacheKey]['lifetime']
        );
    }

    private function generateCacheDriverInfo($tag)
    {
        return [
            'driver'   => $this->parseDriver($tag),
            'lifetime' => $this->parseLifetime($tag)
        ];
    }

    private function parseDriver($tag)
    {
        if (strpos($tag, 'Using ') !== false) {
            $tag = explode('Using ', $tag);

            if (strpos($tag[1], ' for ')) {
                $tag = explode(' for ', $tag[1])[0];
            } else {
                $tag = $tag[1];
            }

            return trim($tag, '.');
        }

        return null;
    }

    private function parseLifetime($tag)
    {
        if (strpos($tag, ' for ')) {
            return trim(explode(' for ', $tag)[1], '.');
        }

        return null;
    }
}