<?php

namespace Model\Configurator\DocComment;
use Model\Entity\Entity;
use Reflector;
use UnexpectedValueException;

/**
 * Uses doc comments to configure an entity.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
class Vo implements DocTagInterface
{
    /**
     * Configures the entity with the specified VO.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The property to configure.
     * @param Entity    $entity The entity being configured.
     * 
     * @return Vo
     */
    public function configure($value, Reflector $refl, Entity $entity)
    {
        $parts = explode(' ', $value, 2);
        
        if (isset($parts[1])) {
            $class = eval('return new ' . $parts[0] . '(' . $parts[1] . ');');
        } else {
            $class = new $parts[0];
        }
        
        // apply the vo
        $entity->setVo($refl->getName(), $class);
        
        // set the default value if it exists
        $this->setDefaultValueIfExists($entity, $refl);
    }
    
    /**
     * Sets the default value for the VO that is specified in the property definition.
     * 
     * @param Entity    $entity The entity to set the default value of.
     * @param Reflector $refl   The property with a potential default value.
     * 
     * @return void
     */
    private function setDefaultValueIfExists(Entity $entity, Reflector $refl)
    {
        $name = $refl->getName();
        $prop = $refl->getDeclaringClass()->getDefaultProperties();
        if (isset($prop[$name])) {
            $entity->__set($name, $prop[$name]);
        }
    }
}