<?php

namespace Model\Configurator;
use InvalidArgumentException;
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

    public function configure($entityOrRepository)
    {
        if ($entityOrRepository instanceof Entity) {
            $this->configureEntity($entityOrRepository);
        } elseif ($entityOrRepository instanceof RepositoryAbstract) {
            $this->configureRepository($entityOrRepository);
        } else {
            throw new InvalidArgumentException(sprintf(
                'Unable to configure instance of "%s" because it is not a valid entity or repository.',
                get_class($entityOrRepository)
            ));
        }
    }

    private function configureEntity($entityOrRepository)
    {
        $refl = new ReflectionClass($entityOrRepository);
        $this->configureFromDocComment($entityOrRepository, $refl);

        foreach ($refl->getProperties() as $prop) {
            $this->configureFromDocComment($entityOrRepository, $prop);
        }
    }

    private function configureRepository($entityOrRepository)
    {
        $refl = new ReflectionClass($entityOrRepository);
        foreach ($refl->getMethods() as $method) {
            $this->configureFromDocComment($entityOrRepository, $method);
        }
    }

    private function configureFromDocComment($entityOrRepository, Reflector $refl)
    {
        $tags = $this->parseDocCommentIntoTags($refl->getDocComment());

        foreach ($tags as $def) {
            $tag = new $this->tags[$def['name']];
            $tag->configure($def['value'], $refl, $entityOrRepository);
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