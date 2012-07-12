<?php

namespace Model\Configurator\DocComment;
use Closure;
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
class AutoTag implements DocTagInterface
{
    /**
     * Configures the entity with the specified VO.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The property to configure.
     * @param Entity    $entity The entity being configured.
     * 
     * @return void
     */
    public function configure($value, Reflector $refl, Entity $entity)
    {
        $entity->setAutoloader($refl->getName(), $value);
    }
}