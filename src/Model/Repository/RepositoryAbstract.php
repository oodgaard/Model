<?php

namespace Model\Repository;

/**
 * Acts as an aggregate for all repository functionality.
 * 
 * @category Repositories
 * @package  Model
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2010 Trey Shugart http://europaphp.org/license
 */
abstract class RepositoryAbstract
{
    use Cacheable;
    use Singleton;
}