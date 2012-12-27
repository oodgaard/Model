<?php

namespace Test;
use Fixture\BenchmarkData;
use Model\Entity\Set;
use Testes\Test\UnitAbstract;

class BenchmarkTest extends UnitAbstract
{
    public function setUp()
    {
        $this->benchmark('hydration');
    }

    public function hydration()
    {
        new Set('Provider\Benchmark\Person', $this->buildData());
    }

    public static function buildData($numPeople = 100, $numAddressesPerPerson = 10)
    {
        $data = [];

        for ($i = 1; $i <= $numPeople; $i++) {
            $data[$i] = [
                'forename'  => 'Forename ' . $i,
                'surname'   => 'Surname ' . $i,
                'email'     => 'email' . $i . '@address' . $i . '.com',
                'password'  => 'password' . $i,
                'residence' => [
                    'street'   => '100 Butts Lane',
                    'city'     => 'Seymour',
                    'state'    => 'BS',
                    'postcode' => '10000',
                    'country'  => 'USA'
                ],
                'addresses' => []
            ];

            for ($ii = 1; $ii <= $numAddressesPerPerson; $ii++) {
                $data[$i]['addresses'][$ii] = [
                    'street'   => $ii . ' Another Street',
                    'city'     => 'Another City',
                    'state'    => 'Another State',
                    'postcode' => '10000',
                    'country'  => 'Australia'
                ];
            }
        }

        return $data;
    }
}