#!/usr/bin/env php
<?php
/**
 * Open list bootstrap file.
 *
 */
require __DIR__ . '/src/autoload.php';
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

use ADiaz\AML\OpenList\commands\Processor;
use ADiaz\AML\OpenList\commands\Receiver;

$config = require(__DIR__ . '/src/OpenList/conf/app.php');

$application = new Application();
$application->add(new Receiver($config));
$application->add(new Processor($config));
$application->run();

