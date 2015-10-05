<?php

$loader = require __DIR__.'/../../vendor/autoload.php';

use Culpa\Tests\Bootstrap\AppFactory;
use Culpa\Tests\Bootstrap\CulpaTest;

CulpaTest::$app = AppFactory::create();
