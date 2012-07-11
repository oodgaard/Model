<?php

namespace Model\Configurator;
use Model\Configurator\DocComment;
use Model\Entity\Entity;
use Reflector;
use ReflectionClass;
use ReflectionProperty;
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
     * The tags and their configurators.
     * 
     * @var array
     */
    private $tags = [];
    
    /**
     * Sets up the doc comment configurator.
     * 
     * @return DocComment
     */
    public function __construct()
    {
        $this->set('auto', new DocComment\AutoTag);
        $this->set('map', new DocComment\MapTag);
        $this->set('valid', new DocComment\ValidTag);
        $this->set('vo', new DocComment\VoTag);
    }
    
    /**
     * Sets a doc tag implementation to use for the specified tag.
     * 
     * @param string                     $name The tag name.
     * @param DocComment\DocTagInterface $tag  The tag instance to handle the tag.
     * 
     * @return DocComment
     */
    public function set($name, DocComment\DocTagInterface $tag)
    {
        $this->tags[$name] = $tag;
        return $this;
    }
    
    /**
     * Configures the specified entity.
     * 
     * @param EntityAbstract $entity The entity to configure.
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
            $part = trim($part, "\r\n\t */");
        }
    }
}