<?php


namespace ADiaz\AML\OpenList\parsers;

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
interface ListInterface
{
    /**
     * Process the list (read, process, set the date and verify entities).
     *
     * Example
     *
     * if ($this->readEntities()) {
     *     $this->processEntities();
     *     $this->setDate();
     *     return $this->verifyEntities();
     * }
     */
    public function run();

    /**
     * Set source path, the file path of the list.
     * this method will be called by the Processor.
     *
     * Suggestion: add a property to the class
     *
     * @param string $path
     */
    public function setSourcePath($path);

    /**
     * Read the content from the file list.
     *
     * Suggestion: add a property to the class to save the content
     *
     * @TODO change the name to setFileContent;
     */
    public function readEntities();

    /**
     * Process entities. It analyses the entities and it parses to classes.
     */
    public function processEntities();

    /**
     * Verify the entities, for example the content or
     * if the amount of entities match with amount from the file if it is provided.
     */
    public function verifyEntities();

    /**
     * Get entities
     * return the entities created.
     */
    public function getEntities();

    /**
     * Set the date when the list was updated, it takes the info from the content of the file if exists.
     *
     * @TODO add an attribute to send the format of the date
     */
    public function setDate();

    /**
     * Get the date of the list. When it was updated.
     * It must be given in Y-m-d format.
     */
    public function getDate();
}
