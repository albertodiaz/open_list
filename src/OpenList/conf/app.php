<?php

$lists = include __DIR__.'/lists.php';

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__).'/../../',
    'listsPath'           => dirname(__DIR__).'/../../lists/',
    'outputPath'          => dirname(__DIR__).'/../../output/',
    'controllerNamespace' => 'ADiaz\AML\OpenList\parsers',
    'dateFormat'          => 'Y-m-d',
    'timezone'            => 'Europe/Madrid',
    'lists'               => $lists,
];

return $config;
