<?php

namespace Test\Vo;
use Model\Vo\Date;
use Testes\Test\UnitAbstract;

class DateTest extends UnitAbstract
{
    private $time = 1262268000;

    private $format = 'Y-m-d\TH:i:s\Z';

    private $timezone = 'UTC';

    private $currentTimezone;

    public function setUp()
    {
        $this->currentTimezone = date_default_timezone_get();
        date_default_timezone_set($this->timezone);
    }

    public function setDateByInteger()
    {
        $date = $this->generateDate();
        $date = $date->translate($this->time);
        $this->assert($date === '2009-12-31T14:00:00Z', 'The date was not set from an integer');
    }

    public function setDateByString()
    {
        $date = $this->generateDate();
        $date = $date->translate(date('Y-m-d H:i:s', $this->time));
        $this->assert($date === '2009-12-31T14:00:00Z', 'The date was not set from a string');
    }

    public function formatDate()
    {
        $date = $this->generateDate('Y-m-d\TH:i:s');
        $date = $date->translate($this->time);
        $this->assert($date === '2009-12-31T14:00:00', 'The date format was not used');
    }

    public function tearDown()
    {
        date_default_timezone_set($this->currentTimezone);
    }

    private function generateDate($format = null, $timezone = null)
    {
        return new Date([
            'format'   => $format ?: $this->format,
            'timezone' => $timezone ?: $this->timezone
        ]);
    }
}