<?php

namespace Provider\Filter;

/**
 * @filter from ns1 using Provider\Filter\TestEntityFromFilter.
 * @filter to ns1 using Provider\Filter\TestEntityToFilter.
 */
class TestEntity extends \Model\Entity\Entity
{
    /**
     * @vo Model\Vo\Boolean
     */
    public $testOneNamespaceFrom;

    /**
     * @vo Model\Vo\Boolean
     */
    public $testManyNamespaceTo;

    /**
     * @vo Model\Vo\Boolean
     * 
     * @filter from ns1.ns2 using Provider\Filter\TestVoFromFilter.
     * @filter to ns1.ns2 using Provider\Filter\TestVoToFilter.
     */
    public $testManyNamespacesFrom;

    /**
     * @vo Model\Vo\Boolean
     * 
     * @filter from ns1.ns2 using Provider\Filter\TestVoFromFilter.
     * @filter to ns1.ns2 using Provider\Filter\TestVoToFilter.
     */
    public $testManyNamespacesTo;
}