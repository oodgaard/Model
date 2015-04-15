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

    public function nullDate()
    {
        $dateTime = $this->generateDate('Y-m-d\TH:i:s');
        $translateResult = $dateTime->translate(null);
        $dateNow = new \DateTime();
        $dateOverTolerance = clone $dateNow;

        $dateOverTolerance->modify('+' . (self::SECONDS_TOLERANCE + 1) . ' seconds');

        // Determine how many seconds we need to be over tolerance
        $secondsDifference = $dateNow->diff($dateOverTolerance)->format('%s');

        // Ensure our logic for testing tolerance is correct
        $this->assert(
            $secondsDifference > self::SECONDS_TOLERANCE,
            sprintf(
                'Difference (%s seconds) did not exceeded tolerance (%s seconds)',
                $secondsDifference, self::SECONDS_TOLERANCE
            )
        );

        // Determine how many seconds elapsed between the creation of the two dates
        $secondsDifference = $dateNow->diff(new \DateTime($translateResult))->format('%s');

        // Ensure generated the dates difference is within the acceptable tolerance
        $this->assert(
            $secondsDifference <= self::SECONDS_TOLERANCE,
            sprintf(
                'Difference (%s seconds) exceeded tolerance (%s seconds)',
                $secondsDifference, self::SECONDS_TOLERANCE
            )
        );

        // Enable allowNull option
        $dateTime = $this->generateDate('Y-m-d\TH:i:s', null, true);
        $translateResult = $dateTime->translate(null);

        // Ensure nulls are allowed
        $this->assert(
            $translateResult === null,
            sprintf('Unexpected return value, expected null got %s', gettype($translateResult))
        );
    }

    public function initNullDate()
    {
        $date = $this->generateDate(null, null, true);

        $this->assert($date->init() === null, 'Date is not null, expected null');
    }

    public function initNotNullDate()
    {
        $date = $this->generateDate(null, null, false);

        $this->assert($date->init() !== null, 'Date is null, expected a date string');
    }

    public function timezoneOnNow()
    {
        $dateTimeDefault = $this->generateDate('Y-m-d\TH:i:s', null, true);
        $dateTimeLocal   = $this->generateDate('Y-m-d\TH:i:s', 'Australia/Brisbane', true);

        $translasteResult1 = $dateTimeDefault->translate('now');
        $translasteResult2 = $dateTimeLocal->translate('now');

        $this->assert($translasteResult1 != $translasteResult2, 'Dates should not match');
    }

    public function timeZoneOnInput()
    {
        $dateTime = $this->generateDate('Y-m-d\TH:i:s O', 'Australia/Brisbane', true);
        $translasteResult = $dateTime->translate('2014-04-16 20:00:00 +0000');

        $this->assert($translasteResult == '2014-04-17T06:00:00 +1000', 'Date was not translated to the correct timezone');


        $dateTime = $this->generateDate('Y-m-d\TH:i:s O', null, true);
        $translasteResult = $dateTime->translate('2014-04-17 6:00:00 +1000');

        $this->assert($translasteResult == '2014-04-16T20:00:00 +0000', 'Date was not translated to the correct timezone');
    }

    public function tranlateToDefaultTimezone()
    {
        date_default_timezone_set('Australia/Brisbane');

        $dateTime = $this->generateDate('Y-m-d\TH:i:s O', null, true, true);
        $translasteResult = $dateTime->translate('2014-04-16 20:00:00 +0000');

        $this->assert($translasteResult == '2014-04-17T06:00:00 +1000', 'Date was not translated to the default timezone');
    }

    public function dontTranlateToDefaultTimezone()
    {
        date_default_timezone_set('Australia/Brisbane');

        $dateTime = $this->generateDate('Y-m-d\TH:i:s O', null, true);
        $translasteResult = $dateTime->translate('2014-04-16 20:00:00 +0000');

        $this->assert($translasteResult == '2014-04-16T20:00:00 +0000', 'Date was not translated to the default timezone');
    }


    public function tearDown()
    {
        date_default_timezone_set($this->currentTimezone);
    }

    private function generateDate($format = null, $timezone = null, $allowNull = false, $convertToDefaultTimezone = false)
    {
        return new Date([
            'format'   => $format ?: $this->format,
            'timezone' => $timezone ?: $this->timezone,
            'allowNull' => $allowNull,
            'convertToDefaultTimezone' => $convertToDefaultTimezone
        ]);
    }
}
