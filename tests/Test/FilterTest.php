<?php

namespace Test;
use Model\Entity\Entity;
use Model\Entity\Set;
use Provider\Filter\TestRepository;

class FilterTest extends \Testes\Test\UnitAbstract
{
    public function filteringEntity()
    {
        $entity = TestRepository::getTestEntity();

        $this->assert($entity instanceof Entity, 'Entity was not returned.');
    }

    public function filteringSet()
    {
        $set = TestRepository::getTestSet();

        $this->assert($set instanceof Set, 'Set not returned.');
    }
}