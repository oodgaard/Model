<?php

namespace Test\Vo;
use Model\Vo\Date;
use Testes\Test\UnitAbstract;

class DateTest extends UnitAbstract
{
    const SECONDS_TOLERANCE = 5;

    private $time = 1262268000;

    private $format = 'Y-m-d\TH:i:s\Z';

    private $timezone = 'UTC';

    private $currentTimezone;

    public function setUp()
    {
        date_default_timezone_set($this->timezone);
        $this->currentTimezone = date_default_timezone_get();
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

    public function nullDate()
    {
        $expectedDate = '2009-12-31T14:00:00';
        $date = $this->generateDate('Y-m-d\TH:i:s');
        $resultantDate = $date->translate(null);
        $dateNow = new \DateTime();
        $dateOverTolerance = clone $dateNow;

        $dateOverTolerance->modify('+' . self::SECONDS_TOLERANCE + 1 . ' seconds');

        $secondsDifference = $dateNow->diff($dateOverTolerance)->format('%s');

        // Ensure our logic for testing tolerance is correct
        $this->assert(
            $secondsDifference > self::SECONDS_TOLERANCE,
            sprintf(
                'Difference (%s seconds) did not exceeded tolerance (%s seconds)',
                $secondsDifference, self::SECONDS_TOLERANCE
            )
        );

        $secondsDifference = $dateNow->diff(new \DateTime($resultantDate))->format('%s');

        // Ensure generated dates are within the acceptable tolerance
        $this->assert(
            $secondsDifference <= self::SECONDS_TOLERANCE,
            sprintf(
                'Difference (%s seconds) exceeded tolerance (%s seconds)',
                $secondsDifference, self::SECONDS_TOLERANCE
            )
        );

        $date = $this->generateDate('Y-m-d\TH:i:s', null, true);
        $resultantDate = $date->translate(null);

        // Ensure nulls are allowed
        $this->assert(
            $resultantDate === null,
            sprintf('Unexpected return value, expected null got %s', gettype($resultantDate))
        );
    }

    public function tearDown()
    {
        date_default_timezone_set($this->currentTimezone);
    }

    private function generateDate($format = null, $timezone = null, $allowNull = false)
    {
        return new Date([
            'format'   => $format ?: $this->format,
            'timezone' => $timezone ?: $this->timezone,
            'allowNull' => $allowNull,
        ]);
    }
}
