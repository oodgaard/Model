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
     * Configures the specified entity.
     * 
     * @param Entity $entity The entity to configure.
     * 
     * @return void
     */
    public function configure(Entity $entity);
}