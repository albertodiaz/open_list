<?php

require __DIR__.'/../../src/autoload.php';

use ADiaz\AML\OpenList\commands\Receiver;
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
class ReceiverTest extends \PHPUnit_Framework_TestCase
{
    protected $project_path;
    protected $list_path;

    protected function setUp()
    {
        $this->list_path = __DIR__.'/../../lists/';

        if (!file_exists($this->list_path)) {
            mkdir($this->list_path, 0777, true);
        }
    }

    /**
     * Execute.
     */
    public function testRun()
    {
        $config = require __DIR__.'/../../src/OpenList/conf/app.php';
        /*
            $application = new Application();
            $application->add(new Receiver($config));
            $application->run();


            $command = $application->find('receive');
            $commandTester = new CommandTester($command);
            $commandTester->execute(array(
                'command'      => $command->getName()
            ));
    */
        //$this->assertRegExp('/'.Receiver::FINISH_OK.'/', $commandTester->getDisplay());

        $this->assertFileExists($this->list_path.'ukhmt/ukhmt.csv');
        $this->assertFileExists($this->list_path.'usofac/usofac.xml');
        $this->assertFileExists($this->list_path.'unsc/unsc.xml');
    }
}
