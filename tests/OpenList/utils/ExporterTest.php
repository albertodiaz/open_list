<?php

namespace ADiaz\AML\OpenList\utils;

use ADiaz\AML\OpenList\models\Entity;

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
class ExporterTest extends \PHPUnit_Framework_TestCase
{
    protected $exporter;
    protected $output_path;

    protected function setUp()
    {
        $this->output_path = __DIR__.'/../../output/';
        $this->exporter = new Exporter($this->output_path);

        if (!file_exists($this->output_path)) {
            mkdir($this->output_path, 0777, true);
        }
    }

    public function testSerialize()
    {
        $entities = array();
        $prefix = 'test-serialize';

        $entities[] = new Entity(1001);
        $entities[] = new Entity(1002);

        $this->exporter->serialize($entities, $prefix);

        //filename
        $filename = $prefix.date('_Y-m-d').'.serialized';

        // check the file
        $this->assertFileExists($this->output_path.$filename);

        // check if it is possible to unserialize
        $this->assertNotFalse(unserialize(file_get_contents($this->output_path.$filename)));

        //remove test file
        unlink($this->output_path.$filename);
    }

    public function testJson()
    {
        $prefix = 'test-json';

        $entities[] = new Entity(1003);
        $entities[] = new Entity(1004);

        $this->exporter->json($entities, $prefix);

        //filename
        $filename = $prefix.date('_Y-m-d').'.json';

        //expected result
        $expectedResult = '[{"external_id":1003},{"external_id":1004}]';

        // check content generated
        $this->assertEquals(file_get_contents($this->output_path.$filename), $expectedResult);

        //remove test file
        unlink($this->output_path.$filename);
    }
}
