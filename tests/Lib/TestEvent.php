<?php

namespace Lib;
use Testes\Event\Test as Event;

class TestEvent
{
    const PADDING = 88;

    public $methodTimer = [];

    public $testTime = 0;

    public $totalTime = 0;

    public function get()
    {
        $event = new Event;
        $runner = $this;

        $event->on('preRun', function($test) {
            echo 'Running Tests for: ' . get_class($test) . PHP_EOL;
        });

        $event->on('preMethod', function ($method, $test) use ($runner) {
            $runner->methodTime[$method]['start'] = microtime(true);
        });

        $event->on('postMethod', function ($method, $test) use ($runner) {
            $runner->methodTime[$method]['stop'] = microtime(true);

            $className = get_class($test);

            $start = $runner->methodTime[$method]['start'];
            $stop = $runner->methodTime[$method]['stop'];
            $time = $stop - $start;
            $runner->testTime += $time;
            $number = (string) number_format($time, 3);

            echo "\033[" .($test->isMethodPassed($method) ? '42m[PASS]' : "41m[FAIL]") . "\033[0m" .
                str_pad(' ' . $className . '::' . $method . ' ', self::PADDING - strlen($number)) .
                $number . PHP_EOL;
        });

        $event->on('postRun', function($test) use ($runner) {
            $number = number_format($runner->testTime, 3);
            $runner->totalTime += $runner->testTime;
            $runner->testTime = 0;

            echo str_pad('Total for ' . get_class($test) . ': ' , self::PADDING + 6 - strlen($number), ' ', STR_PAD_LEFT) .
                "\033[1m" . $number . "\033[0m" . PHP_EOL;
        });

        return $event;
    }
}