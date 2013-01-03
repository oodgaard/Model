<?php

namespace Model\Configurator\DocComment;
use Reflector;

abstract class ConfiguratorAbstract
{
    private $handlers = [];

    private static $cache = [];

    public function configure(Reflector $reflector, $object)
    {
        $cacheKey = get_class($object) . $reflector->getName();

        if (isset(self::$cache[$cacheKey])) {
            $comment = self::$cache[$cacheKey];
        } elseif ($comment = $reflector->getDocComment()) {
            $comment = self::$cache[$cacheKey] = new DocComment($comment);
        } else {
            return;
        }

        foreach ($comment as $tag) {
            if (isset($this->handlers[$tag->getName()])) {
                $this->handlers[$tag->getName()]($tag, $reflector, $object);
            }
        }
    }

    public function addTagHandler($name, callable $handler)
    {
        $this->handlers[$name] = $handler;
        return $this;
    }
}