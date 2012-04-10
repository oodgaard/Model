<?php

namespace Model\Configurator\DocComment;
use Model\Entity\Entity;
use Reflector;

/**
 * Uses doc comments to configure an entity.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
interface DocTagInterface
{
    /**
     * Configures the entity.
     * 
     * @param string    $value  The doc tag string minus the tag name.
     * @param Reflector $refl   The reflector object representing the item with the corresponding tag.
     * @param Entity    $entity The entity being configured.
     * 
     * @return Vo
     */
    public function configure($value, Reflector $refl, Entity $entity);
}