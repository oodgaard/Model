<?php

namespace Test\Cache;
use Provider\Cache\Repository;
use Testes\Test\UnitAbstract;

class RepositoryCacheTest extends UnitAbstract
{
    public function caching()
    {
        $repository = new Repository;

        $start = microtime(true);
        $content = $repository->getByContentId(1);
        $first = microtime(true) - $start;

        $start = microtime(true);
        $content = $repository->getByContentId(1);
        $second = microtime(true) - $start;

        $this->assert($second < $first, 'Expected cached content to be retrieved faster');
    }

    public function entity()
    {
        $repository = new Repository;

        // add to cache
        $repository->getById(1);

        // retrieve from cache
        $entity = $repository->getById(1);

        $this->assert(isset($entity->id) && $entity->id == 1, 'Invalid cached content returned');
    }
}