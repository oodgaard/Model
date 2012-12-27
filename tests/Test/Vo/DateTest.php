<?php

namespace Test\Vo;
use Model\Vo\Date;
use Testes\Test\UnitAbstract;

class DateTest extends UnitAbstract
{
    private $time = 1262268000;

    private $format = 'Y-m-d\TH:i:s\Z';

    private $timezone = 'Australia\Sydney';

    public function setDateByInteger()
    {
        $date = $this->generateDate();
        $date->set($this->time);
        $this->assert($date->get() === '2010-01-01T00:00:00Z', 'The date was not set from an integer');
    }

    public function setDateByString()
    {
        $date = $this->generateDate();
        $date->set(date('Y-m-d H:i:s', $this->time));
        $this->assert($date->get() === '2010-01-01T00:00:00Z', 'The date was not set from a string');
    }

    public function formatDate()
    {
        $date = $this->generateDate('Y-m-d\TH:i:s');
        $date->set($this->time);
        $this->assert($date->get() === '2010-01-01T00:00:00', 'The date format was not used');
    }

    private function generateDate($format = null, $timezone = null)
    {
        return new Date([
            'format'   => $format ?: $this->format,
            'timezone' => $timezone ?: $this->timezone
        ]);
    }
}