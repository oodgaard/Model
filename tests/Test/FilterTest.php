<?php

namespace Test;
use Model\Entity\Entity;
use Model\Entity\Set;
use Provider\Filter\TestRepository;

class FilterTest extends \Testes\Test\UnitAbstract
{
    public function filteringToAndFromOneAndManyNamespaces()
    {
        $one  = TestRepository::getTestEntityUsingOneNamespace();
        $many = TestRepository::getTestSetUsingOneNamespace();

        $this->assert($one->testOneNamespaceFrom);
        $this->assert(!$one->testManyNamespacesFrom);
        $this->assert($many[0]->testOneNamespaceFrom);
        $this->assert($many[1]->testOneNamespaceFrom);
        $this->assert(!$many[0]->testManyNamespacesFrom);
        $this->assert(!$many[1]->testManyNamespacesFrom);

        $one  = $one->to('ns1.ns2');
        $many = $many->to('ns1.ns2');

        $this->assert($one['testManyNamespacesFrom']);
        $this->assert($many[0]['testManyNamespacesFrom']);
        $this->assert($many[1]['testManyNamespacesFrom']);
    }
}