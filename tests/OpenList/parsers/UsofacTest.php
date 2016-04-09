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
class UsofacTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $output_file;
    protected $output_folder;

    protected function setUp()
    {
        ini_set('memory_limit', '350M');

        // output file and folder
        $this->output_folder = __DIR__.'/../../../output/';
        $this->output_file = $this->output_folder.'test_ofac_'.date('Y-m-d').'.json';

        // parser
        $this->parser = new Usofac();
        //@Todo use the helper to create the fixtures and use it
        $this->parser->setSourcePath(__DIR__.'/../fixtures/lists/usofac_2016-03-25.xml');
        $this->parser->run();

        // export
        $exporter = new Exporter($this->output_folder);
        $exporter->json($this->parser->getEntities(), 'test_ofac');
    }

    /**
     * Test run.
     */
    public function testCheckTotalEntities()
    {
        $entitiesOk = json_decode(file_get_contents($this->output_file), true);

        $this->assertEquals(count($this->parser->getEntities()), count($entitiesOk), 'The number of sanctions should be the same');
    }

    /*
     * Validate an entity
     */
    public function testValidateEntity()
    {
        $entitiesOk = json_decode(file_get_contents($this->output_file), true);

        $found = false;

        foreach ($entitiesOk as $entity) {
            if ($entity['external_id'] == '36'
                && $entity['last_name'] === 'AEROCARIBBEAN AIRLINES'
                && $entity['type'] === 'Entity'
                && $entity['program_list'][0]['program'] === 'CUBA'
                && $entity['aka_list'][0]['last_name'] === 'AERO-CARIBBEAN'
              ) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'An entity was not found');
    }
}
