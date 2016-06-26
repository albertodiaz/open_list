<?php

namespace ADiaz\AML\OpenList\commands;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

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
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $project_path;
    protected $output_path;

    protected function setUp()
    {
        $list_path = __DIR__.'/../../../lists/';

        if (!file_exists($list_path)) {
            mkdir($list_path, 0777, true);
        }

        $this->output_path = __DIR__.'/../../../output/';

        if (!file_exists($this->output_path)) {
            mkdir($this->output_path, 0777, true);
        }
    }

    /**
     * Execute.
     */
     /*
    public function testRun()
    {
        $config = require __DIR__.'/../../../src/OpenList/conf/app.php';

        // Before execute this test, it is necessary to execute the command receive
        $applicationReceiver = new Application();
        $applicationReceiver->add(new Receiver($config));

        $commandReceiver = $applicationReceiver->find('receive');
        $commandTesterReceiver = new CommandTester($commandReceiver);
        $commandTesterReceiver->execute(array(
            'command' => $commandReceiver->getName(),
        ));

        // Start processing
        $application = new Application();
        $application->add(new Processor($config));

        $command = $application->find('process');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        // check console result
        $this->assertRegExp('/'.Processor::FINISH_OK.'/', $commandTester->getDisplay());

        // check if exported files exists
        $this->assertFileExists($this->output_path.'all'.date('_Y-m-d').'.json');
        $this->assertFileExists($this->output_path.'ukhmt'.date('_Y-m-d').'.json');
        $this->assertFileExists($this->output_path.'usofac'.date('_Y-m-d').'.json');
        $this->assertFileExists($this->output_path.'unsc'.date('_Y-m-d').'.json');
        $this->assertFileExists($this->output_path.'ukhmt'.date('_Y-m-d').'.serialized');
        $this->assertFileExists($this->output_path.'usofac'.date('_Y-m-d').'.serialized');
        $this->assertFileExists($this->output_path.'unsc'.date('_Y-m-d').'.serialized');

        // check the size of the files exported
        $this->assertTrue(filesize($this->output_path.'all'.date('_Y-m-d').'.json') > 10000);
        $this->assertTrue(filesize($this->output_path.'ukhmt'.date('_Y-m-d').'.json') > 10000);
        $this->assertTrue(filesize($this->output_path.'usofac'.date('_Y-m-d').'.json') > 10000);
        $this->assertTrue(filesize($this->output_path.'unsc'.date('_Y-m-d').'.json') > 10000);
        $this->assertTrue(filesize($this->output_path.'ukhmt'.date('_Y-m-d').'.serialized') > 10000);
        $this->assertTrue(filesize($this->output_path.'usofac'.date('_Y-m-d').'.serialized') > 10000);
        $this->assertTrue(filesize($this->output_path.'unsc'.date('_Y-m-d').'.serialized') > 10000);
        $this->assertTrue(filesize($this->output_path.'all'.date('_Y-m-d').'.json') > 10000);
    }
    */
}
