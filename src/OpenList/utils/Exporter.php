<?php

namespace ADiaz\AML\OpenList\utils;

/**
 * This file is part of the OpenList Parser utility.
 *
 * @category  PHP
 *
 * @author    Alberto Diaz <alberto@tytem.com>
 * @copyright 2016 Alberto Diaz <alberto@tytem.com>
 * @license   This source file is subject to the MIT license that is bundled
 *
 * @version Release: @package_version@
 *
 * @link http://tytem.com
 */
class Exporter
{
    /**
     * @var string output files path
     */
    public $output_path;

    /**
     * @var string date format to be used in the date function such as 'Y-m-d'
     */
    public $date_format;

    /**
     * Exporter constructor.
     *
     * @param string $outputPath
     * @param string $date_format
     */
    public function __construct($outputPath, $date_format = 'Y-m-d')
    {
        $this->output_path = $outputPath;
        $this->date_format = $date_format;
    }

    /**
     * Serialize the entities.
     *
     * @param array  $entities
     * @param string $listId   name of the list to serialized
     */
    public function serialize($entities, $listId)
    {
        $nameFile = $this->output_path.$listId.'_'.date($this->date_format).'.serialized';
        file_put_contents($nameFile, serialize($entities));
    }

    /**
     * Create a json file.
     *
     * @param array  $entities
     * @param string $listId   name of the list to encode
     */
    public function json($entities, $listId)
    {
        $nameFile = $this->output_path.$listId.'_'.date($this->date_format).'.json';
        file_put_contents($nameFile, Utils::removeEmptyAndNullJson(json_encode($entities)));
    }
}
