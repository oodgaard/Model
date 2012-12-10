<?php

namespace Model\Configurator;
use Model\Configurator\DocComment;
use Model\Entity\Entity;
use Model\Repository\RepositoryAbstract;
use Reflector;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

class DocComment implements ConfiguratorInterface
{
    private $tags = [];

    public function set($name, DocComment\DocTagInterface $tag)
    {
        $this->tags[$name] = $tag;
        return $this;
    }

    public function configure(ConfigurableInterface $configurable)
    {
        if ($configurable instanceof Entity) {
            $this->configureEntity($configurable);
        } elseif ($configurable instanceof RepositoryAbstract) {
            $this->configureRepository($configurable);
        }
    }

    private function configureEntity(ConfigurableInterface $configurable)
    {
        $refl = new ReflectionClass($configurable);
        $this->configureFromDocComment($configurable, $refl);

        foreach ($refl->getProperties() as $prop) {
            $this->configureFromDocComment($configurable, $prop);
        }
    }

    private function configureRepository(ConfigurableInterface $configurable)
    {
        $refl = new ReflectionClass($configurable);
        foreach ($refl->getMethods() as $method) {
            $this->configureFromDocComment($configurable, $method);
        }
    }

    private function configureFromDocComment(ConfigurableInterface $configurable, Reflector $refl)
    {
        $tags = $this->parseDocCommentIntoTags($refl->getDocComment());

        foreach ($tags as $def) {
            $tag = new $this->tags[$def['name']];
            $tag->configure($def['value'], $refl, $configurable);
        }
    }

    private function parseDocCommentIntoTags($comment)
    {
        $tags  = [];
        $parts = preg_split('/\* @/', $comment);
        
        // remove unnecessary parts
        $this->formatParts($parts);
        
        // instantiate each tag
        foreach ($parts as $part) {
            $split = explode(' ', $part, 2);
            
            // if the tag class doesn't exist don't do anything
            if (!isset($this->tags[$split[0]])) {
                continue;
            }
            
            // default the split[1] value
            if (!isset($split[1])) {
                $split[1] = null;
            }
            
            // instantate the tag class
            $tags[] = [
                'name'  => $split[0],
                'value' => $split[1]
            ];
        }
        
        return $tags;
    }

    private function formatParts(array &$parts)
    {
        // remove the first element
        array_shift($parts);
        
        // remove the trailing comment and whitespace on the last element
        foreach ($parts as &$part) {
            $part = trim($part, "\r\n\t */");
        }
    }
}