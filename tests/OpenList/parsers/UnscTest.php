<?php

namespace ADiaz\AML\OpenList\parsers;

use ADiaz\AML\OpenList\models\Entity;
use ADiaz\AML\OpenList\utils\Exporter;

/**
 * This file is part of the OpenList Parser utility.
 *
 *
 * @category  PHP
 *
 * @author    Alberto Diaz <alberto@tytem.com>
 * @copyright 2016 Alberto Diaz <alberto@tytem.com>
 * @license   This source file is subject to the MIT license that is bundled
 *
 * @version   Release: @package_version@
 *
 * @link      http://tytem.com
 */
class UnscTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $output_file;
    protected $output_folder;

    protected function setUp()
    {
        // output file and folder
        $this->output_file = __DIR__.'/../fixtures/output/test_unsc_2016-03-26.json';

        // parser
        $this->parser = new Unsc();
        $this->parser->setSourcePath(__DIR__.'/../fixtures/lists/unsc_2016-03-25.xml');
        $this->parser->run();

        // export
        $exporter = new Exporter($this->output_folder);
        $exporter->json($this->parser->getEntities(), 'test_unsc');
    }

    /**
     * Test run.
     */
    public function testCheckTotalEntities()
    {
        $entitiesProcessed = json_decode(file_get_contents($this->output_file), true);

        $this->assertEquals(count($this->parser->getEntities()), count($entitiesProcessed), 'The number of sanctions should be the same');
    }

    /**
     * Validate first_name and last name.
     */
    public function testValidateEmptyFields()
    {
        $this->validateEmptyFields(66560, ['address_list', 'place_of_birth_list', 'id_list']);
        $this->validateEmptyFields(97463, ['address_list', 'place_of_birth_list', 'id_list']);
    }

    /**
     * Validate first_name and last name.
     */
    public function testValidateName()
    {
        $this->validateFields(66560, ['first_name' => 'Gene VonRueden', 'last_name' => 'Macejkovic']);
        $this->validateFields(77391, ['first_name' => 'Dr. Francisca Ritchie I', 'last_name' => 'Quitzon']);
        $this->validateFields(44675, ['first_name' => 'Izabella Hills', 'last_name' => 'Kohler']);
        $this->validateFields(97463, ['first_name' => 'Lorena Powlowski', 'last_name' => 'Ward']);
    }

    /**
     * Validate position.
     */
    public function testValidatePosition()
    {
        $this->validateFields(66560, ['position' => 'General/IRGC officer']);
        $this->validateFields(77391, ['position' => 'Major General/Commander, IRGC (Pasdaran)']);
        $this->validateFields(32760, ['position' => 'Brigadier-General/Former Deputy Chief of Armed Forces General Staff for Logistics and Industrial Research/Head of State Anti-Smuggling Headquarters']);
    }

    /**
     * @param $id
     * @param array $fields it must be an associate value 'name_field'=>'value'
     */
    protected function validateFields($id, $fields)
    {
        $entitiesProcessed = json_decode(file_get_contents($this->output_file), true);

        $found = false;

        foreach ($entitiesProcessed as $entity) {
            if ($entity['external_id'] === (string) $id) {
                foreach ($fields as $key => $value) {
                    $this->assertEquals($entity[$key], $value, "A value of a entity does not match. Found: {$entity[$key]} expected: $value");
                }
                $found = true;
            }
        }
        $this->assertTrue($found, "An entity was not found, entity id: $id");
    }

    /**
     * Validate empty values of an entity.
     *
     * @param $id
     * @param array $fields it must be an associate value 'name_field'=>'value'
     */
    protected function validateEmptyFields($id, $fields)
    {
        $entitiesProcessed = json_decode(file_get_contents($this->output_file), true);

        $found = false;

        foreach ($entitiesProcessed as $entity) {
            if ($entity['external_id'] === (string) $id) {
                $fieldsUsed = array_keys($entity);
                $arrayIntersect = array_intersect($fieldsUsed, $fields);

                $this->assertTrue(count($arrayIntersect) === 0);
                $found = true;
            }
        }
        $this->assertTrue($found, "An entity was not found, entity id: $id");
    }

    /**
     * Validate Akas.
     */
    public function testValidateAkas()
    {
        $entitiesProcessed = json_decode(file_get_contents($this->output_file), true);

        $found = false;

        foreach ($entitiesProcessed as $entity) {
            if ($entity['external_id'] === '66560'
                && $entity['aka_list'][0]['first_name'] === 'Mohammad Bakr Zolqadr'
                && $entity['aka_list'][1]['first_name'] === 'Mohammad Bakr Zolkadr'
                && $entity['aka_list'][2]['first_name'] === 'Mohammad Baqer Zolqadir'
                && $entity['aka_list'][3]['first_name'] === 'Mohammad Baqer Zolqader'
            ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'An entity was not found');
    }
}
