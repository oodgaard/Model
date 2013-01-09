<?php

namespace Test\Vo;
use ArrayObject;
use Model\Vo\Set;
use stdClass;
use Testes\Test\UnitAbstract;

class SetTest extends UnitAbstract
{
    public function arr()
    {
        $set = new Set;

        $array = $set->translate([
            'test1' => 'value1',
            'test2' => 'value2'
        ]);

        $this->assert($array['test1'] === 'value1', 'Value not set.');
        $this->assert($array['test2'] === 'value2', 'Value not set.');
    }

    public function object()
    {
        $set = new Set;
        $obj = new stdClass;

        $obj->test1 = 'value1';
        $obj->test2 = 'value2';

        $array = $set->translate($obj);

        $this->assert($array['test1'] === 'value1', 'Value not set.');
        $this->assert($array['test2'] === 'value2', 'Value not set.');
    }

    public function traversable()
    {
        $set = new Set;

        $array = $set->translate(new ArrayObject([
            'test1' => 'value1',
            'test2' => 'value2'
        ]));

        $this->assert($array['test1'] === 'value1', 'Value not set.');
        $this->assert($array['test2'] === 'value2', 'Value not set.');
    }
}