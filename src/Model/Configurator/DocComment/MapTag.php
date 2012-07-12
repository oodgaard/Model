<?php

namespace Model\Configurator\DocComment;
use Model\Entity\Entity;
use Model\Mapper\MapperArray;
use Reflector;

/**
 * Uses doc comments to configure an entity.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
class MapTag implements DocTagInterface
{
    /**
     * Cache of mappers.
     * 
     * @var array
     */
    private static $cache = [];
    
    /**
     * Configures the entity with the specified VO.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The reflector object representing the item with the corresponding tag.
     * @param Entity    $entity The entity being configured.
     * 
     * @return void
     */
    public function configure($value, Reflector $refl, Entity $entity)
    {
        // parse and gather information
        $parts = explode(' ', $value);
        $name  = array_shift($parts);
        $key   = $refl->getName() . $name;
        
        // if it already exists in the cache, just return in
        if (isset(self::$cache[$key])) {
            $entity->setMapper($name, self::$cache[$key]);
            return;
        }
        
        // use a map array
        $mapArr = new MapperArray;
        foreach ($parts as $class) {
            if ($class = trim($class)) {
                $mapArr->add(new $class);
            }
        }
        
        // apply the mapper
        $entity->setMapper($name, $mapArr);
        
        // update the cache
        self::$cache[$key] = $mapArr;
    }
}