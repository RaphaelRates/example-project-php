<?php

require __DIR__ . '/../vendor/autoload.php';
include __DIR__.'/../bootstrap/bootstrap.php';

use NodacWeb\Core\App;
use NodacWeb\Core\Config;

$app = new App();
Config::getInstance();
$app->run();


?>