<?php

namespace Model\Configurator\DocComment;

class DocTag implements DocTagInterface
{
    private $name;

    private $value;

    public function __construct($definition)
    {
        $parts = explode(' ', $definition, 2);

        $this->name = trim($parts[0]);
        
        if (isset($parts[1])) {
            $this->value = trim($parts[1]);
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
}