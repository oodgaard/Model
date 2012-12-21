<?php

namespace Test;
use Fixture\BenchmarkData;
use Model\Entity\Set;
use Testes\Test\UnitAbstract;

class BenchmarkTest extends UnitAbstract
{
    public function setUp()
    {
        $this->setFixture('data', new BenchmarkData);
        $this->benchmark('hydration');
    }

    public function hydration()
    {
        new Set('Provider\BenchmarkEntity', $this->getFixture('data')->data());
    }
}