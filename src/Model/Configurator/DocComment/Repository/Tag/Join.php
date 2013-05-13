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
        $cache = $tag->getValue();

        if (isset(self::$cache[$cache])) {
            $parsed = self::$cache[$cache];
        } else {
            $parsed = $this->parse($tag->getValue());
        }

        var_dump($parsed);

        if (!method_exists($repository, $parsed['call'])) {
            throw new InvalidArgumentException(sprintf(
                'Cannot join "%s" using "%s" from "%s" in "%s" because the method "%s" does not exist.',
                $parsed['field'],
                $parsed['call'],
                $method->getName()
                get_class($repository),
                $parsed['call']
            ));
        }

        $repository->addJoin($method->getName(), $parsed['call'], $parsed['field']);
    }

    private function parse($value)
    {
        $value = explode(' using ');

        return [
            'call'  => trim($value[1]),
            'field' => trim($value[0])
        ];
    }
}