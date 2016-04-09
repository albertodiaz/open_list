<?php

namespace ADiaz\SC;

/**
 * This file is part of the OpenList Parser utility.
 *
 * @category PHP
 *
 * @author    Alberto Diaz <alberto@tytem.com>
 * @copyright 2016 Alberto Diaz <alberto@tytem.com>
 * @license   This source file is subject to the MIT license that is bundled
 *
 * @version Release: @package_version@
 *
 * @link http://tytem.com
 */
require 'src/autoload.php';

$filename = 'C:\xampp\htdocs\Performance\12-02-2016-12_30_17.serialized';

$entities = unserialize(file_get_contents($filename));

foreach ($entities as $entity) {
    echo " $entity->external_id  $entity->last_name \n";
}
