<?php

use Testes\Output\Cli as Output;

error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 'on');

require dirname(__FILE__) . '/../lib/Model/Autoloader.php';
require dirname(__FILE__) . '/../lib/Testes/Autoloader.php';
\Model\Autoloader::register();
\Testes\Autoloader::register(dirname(__FILE__) . '/../tests');

$test = new Test;
$out  = new Output;

echo $out->render($test->run());