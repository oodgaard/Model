<?php

namespace Provider\Filter;

class TestRepository extends \Model\Repository\RepositoryAbstract
{
    /**
     * @ensure Provider\Filter\TestEntity using test.
     */
    protected function getTestEntity()
    {
        return [
            'testVoFrom' => false
        ];
    }

    /**
     * @ensure Set of Provider\Filter\TestEntity using test.ns.
     */
    protected function getTestSet()
    {
        return [[
            'testVoFrom' => false
        ], [
            'testVoFrom' => false
        ]];
    }
}