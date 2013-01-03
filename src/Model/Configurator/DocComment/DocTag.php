<?php

namespace Model\Configurator\DocComment;

class DocTag implements DocTagInterface
{
    private $name;

    private $value;

    private static $cache = [];

    public function __construct($definition)
    {
        $parts = $this->parseDefinition($definition);

        $this->name = $parts[0];
        
        if (isset($parts[1])) {
            $this->value = $parts[1];
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    private function parseDefinition($definition)
    {
        if (isset(self::$cache[$definition])) {
            return self::$cache[$definition];
        }

        $parts    = explode(' ', $definition, 2);
        $parts[0] = trim($parts[0]);
        $parts[1] = isset($parts[1]) ? trim($parts[1]) : null;

        return self::$cache[$definition] = $parts;
    }
}