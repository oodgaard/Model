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

    public function entityMemCache()
    {
        $repository = new Repository;

        // add to cache
        $first = $repository->getByIdMemCache(1);

        // retrieve from cache
        $second = $repository->getByIdMemCache(1);

        $this->assert(isset($second->contentId) && $second->contentId == 1, 'Invalid cached content returned');

        $diff = array_diff($first->to(), $second->to());

        $this->assert(count($diff) == 0, 'Invalid cached content returned');
    }

    public function entityPhpCache()
    {
        $repository = new Repository;

        // add to cache
        $first = $repository->getByIdPhpCache(2);

        // retrieve from cache
        $second = $repository->getByIdPhpCache(2);

        $this->assert(isset($second->id), 'Invalid cached content returned');

        $diff = array_diff($first->to(), $second->to());

        $this->assert(count($diff) == 0, 'Invalid cached content returned');
    }
}