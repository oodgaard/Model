<?php

namespace Provider\Filter;

/**
 * @filter from test.ns using Provider\Filter\TestEntityFromFilter.
 * @filter to test.ns using Provider\Filter\TestEntityToFilter.
 */
class TestEntity extends \Model\Entity\Entity
{
    /**
     * @vo Model\Vo\Boolean
     */
    public $testEntityFrom;

    /**
     * @vo Model\Vo\Boolean
     */
    public $testEntityTo;

    /**
     * @vo Model\Vo\Boolean
     * 
     * @filter from test.ns using Provider\Filter\TestVoFromFilter.
     * @filter to test.ns using Provider\Filter\TestVoToFilter.
     */
    public $testVoFrom;

    /**
     * @vo Model\Vo\Boolean
     * 
     * @filter from test.ns using Provider\Filter\TestVoFromFilter.
     * @filter to test.ns using Provider\Filter\TestVoToFilter.
     */
    public $testVoTo;
}