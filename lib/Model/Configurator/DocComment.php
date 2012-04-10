<?php

namespace Model\Configurator;
use Model\Configurator\DocComment;
use Model\Entity\Entity;
use Reflector;
use ReflectionClass;
use RuntimeException;

/**
 * Uses doc comments to configure an entity.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
class DocComment implements ConfiguratorInterface
{
    /**
     * The tags to use for configuration.
     * 
     * @var array
     */
    private $tags = [
        'map' => 'Model\Configurator\DocComment\MapTag',
        'var' => 'Model\Configurator\DocComment\VarTag'
    ];
    
    /**
     * Sets a doc tag implementation to use for the specified tag.
     * 
     * @param string $name  The tag name.
     * @param string $class The class to handle the tag.
     * 
     * @return DocComment
     */
    public function set($name, $class)
    {
        if (!is_subclass_of($class, 'Model\Entity\Configurator\DocComment\DocTagInterface')) {
            throw new RuntimeException(
                'The specified tag configurator must implement "Model\Entity\Configurator\DocComment\DocTagInterface".'
            );
        }
        
        $this->tags[$name] = $class;
        
        return $this;
    }
    
    /**
     * Configures the specified entity.
     * 
     * @param Entity $entity The entity to configure.
     * 
     * @return DocComment
     */
    public function configure(Entity $entity)
    {
        $refl = new ReflectionClass($entity);
        $this->configureFromDocComment($entity, $refl);
        foreach ($refl->getProperties() as $prop) {
            $this->configureFromDocComment($entity, $prop);
        }
    }
    
    /**
     * Passes on the configuration to the tags.
     * 
     * @param Entity    $entity The entity to configure.
     * @param Reflector $refl   The reflection object corresponding to the doc comment's element.
     * 
     * @return void
     */
    private function configureFromDocComment(Entity $entity, Reflector $refl)
    {
        $tags = $this->parseDocCommentIntoTags($refl->getDocComment());
        foreach ($tags as $def) {
            $tag = new $this->tags[$def['name']];
            $tag->configure($def['value'], $refl, $entity);
        }
    }
    
    /**
     * Parses the comment into an array of doc tag strings.
     * 
     * @param string $comment The comment to parse.
     * 
     * @return array
     */
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
            
            // instantate the tag class
            $tags[] = [
                'name'  => $split[0],
                'value' => $split[1]
            ];
        }
        
        return $tags;
    }
    
    /**
     * Formats the doc comment tag strings into parts that can be passed onto the tag implementations.
     * 
     * @param array $parts The parts to pass on.
     * 
     * @return void
     */
    private function formatParts(array &$parts)
    {
        // remove the first element
        array_shift($parts);
        
        // remove the trailing comment and whitespace on the last element
        foreach ($parts as &$part) {
            $part = trim($part, "\r\n */");
        }
    }
}