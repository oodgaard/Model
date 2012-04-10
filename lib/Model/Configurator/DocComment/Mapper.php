<?php

namespace Model\Configurator\DocComment;
use Model\Configurator\ConfiguratorInterface;
use Model\Entity\Entity;
use Model\Mapper\MapperInterface;
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
class Mapper implements DocTagInterface
{
    /**
     * Configures the entity with the specified VO.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The class to configure.
     * @param Entity    $entity The entity being configured.
     * 
     * @return Vo
     */
    public function configure($value, Reflector $refl, Entity $entity)
    {
        $parts = explode(' ', $value, 2);
        $name  = trim($parts[0]);
        $class = trim($parts[1]);
        $class = new $class;
        
        if (!$class instanceof MapperInterface) {
            throw new UnexpectedValueException(
                'Mapper "' . get_class($class) . '" instance must derive from Model\Mapper\MapperInterface.'
            );
        }
        
        $entity->setMapper($name, $class);
    }
}