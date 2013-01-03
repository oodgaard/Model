<?php

namespace Model\Configurator\DocComment\Entity\Tag;
use InvalidArgumentException;
use Model\Configurator\DocComment\DocTagInterface;
use Model\Entity\Entity;
use ReflectionClass;

class Filter
{
    public function __invoke(DocTagInterface $tag, ReflectionClass $class, Entity $entity)
    {
        $info = $this->parseFilterInformation($tag->getValue());

        if ($info['direction'] === 'to') {
            $entity->addExportFilter($info['name'], new $info['class']);
        } else {
            $entity->addImportFilter($info['name'], new $info['class']);
        }
    }

    private function parseFilterInformation($tag)
    {
        preg_match('/(from|to) ([a-z0-9.]+) using ([a-z0-9\\\\_]+)\.?/i', $tag, $parts);
        array_shift($parts);

        if (!$parts) {
            throw new InvalidArgumentException('The @filter tag "' . $tag . '" must be in the format of "[to / from] [dot-notated name] using [class].".');
        }

        $parts[0] = strtolower($parts[0]);

        return [
            'direction' => $parts[0],
            'name'      => $parts[1],
            'class'     => $parts[2]
        ];
    }
}