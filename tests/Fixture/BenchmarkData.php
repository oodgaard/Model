<?php

namespace Fixture;
use Testes\Fixture\FixtureAbstract;

class BenchmarkData extends FixtureAbstract
{
    public static function generateData()
    {
        $data = [];

        for ($i = 0; $i < 1000; $i++) {
            $data[$i] = [];

            for ($ii = 1; $ii <= 10; $ii++) {
                $data[$i]['name' . $ii] = 'value' . $ii;
            }
        }

        return $data;
    }
}