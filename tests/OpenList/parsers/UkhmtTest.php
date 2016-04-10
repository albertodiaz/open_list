<?php

namespace ADiaz\AML\OpenList\parsers;

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
class UkhmtTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $output_file;
    protected $output_folder;

    protected function setUp()
    {
        // output file and folder
        $this->output_folder = __DIR__.'/../../../output/';
        $this->output_file = $this->output_folder.'test_ukhmt_'.date('Y-m-d').'.json';

        // parser
        $this->parser = new Ukhmt();
        $this->parser->setSourcePath(__DIR__.'/../fixtures/lists/ukhmt_2016-03-25.csv');
        $this->parser->run();

        // export
        $exporter = new Exporter($this->output_folder);
        $exporter->json($this->parser->getEntities(), 'test_ukhmt');
    }

    public function testRun()
    {
        $this->parser->run();

        $entitiesOk = json_decode(file_get_contents($this->output_file), true);

        $this->assertEquals(count($this->parser->getEntities()), count($entitiesOk), 'The number of sanctions should be the same');
    }
}
