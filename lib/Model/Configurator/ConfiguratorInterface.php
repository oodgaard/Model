<?php

namespace Model\Configurator;
use Model\Entity\Entity;

/**
 * Interface defining how entities should be configured.
 * 
 * @category Configurators
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  http://europaphp.org/license
 */
interface ConfiguratorInterface
{
    /**
     * Sets a doc tag implementation to use for the specified tag.
     * 
     * @param string $name  The tag name.
     * @param string $class The class to handle the tag.
     * 
     * @return DocComment
     */
    public function configure(Entity $entity);
}