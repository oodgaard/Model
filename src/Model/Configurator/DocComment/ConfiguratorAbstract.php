<?php

namespace Model\Configurator\DocComment;
use Reflector;

abstract class ConfiguratorAbstract
{
    private $handlers = [];

    public function configure(Reflector $reflector, $object)
    {
        $comment = new DocComment($reflector->getDocComment());

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