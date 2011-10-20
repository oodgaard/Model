<?php

use Model\Autoloader as Ma;
use Testes\Autoloader as Ta;
use Testes\Output\Cli as Output;
use Test as Test;

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 'on');

$base = dirname(__FILE__) . '/../';

require $base . 'lib/Model/Autoloader.php';
require $base . 'vendor/Testes/lib/Testes/Autoloader.php';

Ma::register();
Ta::register($base . 'tests');

$test = new Test;
$out  = new Output;

echo $out->render($test->run());