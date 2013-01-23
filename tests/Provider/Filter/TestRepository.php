<?php

namespace Provider\Filter;

class TestRepository extends \Model\Repository\RepositoryAbstract
{
    /**
     * @ensure Provider\Filter\TestEntity using ns1.
     */
    protected function getTestEntityUsingOneNamespace()
    {
        return [
            'testOneNamespaceFrom' => false
        ];
    }

    /**
     * @ensure Set of Provider\Filter\TestEntity using ns1.
     */
    protected function getTestSetUsingOneNamespace()
    {
        return [[
            'testOneNamespaceFrom' => false
        ], [
            'testOneNamespaceFrom' => false
        ]];
    }

    /**
     * @ensure Provider\Filter\TestEntity using ns1.ns2.
     */
    protected function getTestEntityUsingManyNamespaces()
    {
        return [
            'testManyNamespacesFrom' => false
        ];
    }

    /**
     * @ensure Set of Provider\Filter\TestEntity using ns1.ns2.
     */
    protected function getTestSetUsingManyNamespaces()
    {
        return [[
            'testManyNamespacesFrom' => false
        ], [
            'testManyNamespacesFrom' => false
        ]];
    }
}