<?php

namespace ADiaz\AML\OpenList\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ADiaz\AML\OpenList\utils\Exporter;

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
class Processor extends Command
{
    const FINISH_OK = 'The lists were processed';

    protected $list_path;
    protected $output_path;
    protected $controller_namespace;
    protected $lists;
    protected $date_format;

    public function __construct($conf)
    {
        $this->list_path = $conf['listsPath'];
        $this->output_path = $conf['outputPath'];
        $this->controller_namespace = $conf['controllerNamespace'];
        $this->lists = $conf['lists'];
        $this->date_format = $conf['dateFormat'];

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('process')
            ->setDescription('Process the lists')
            ->addArgument(
                'lists',
                InputArgument::IS_ARRAY,
                'Which lists do you want to process (separate multiple names with a space)?'
            )
            ->addOption(
                'disable-serialize',
                null,
                InputOption::VALUE_NONE,
                'If set, the serialized version will not be created'
            )
            ->addOption(
                'disable-all',
                null,
                InputOption::VALUE_NONE,
                'If set, the combined file will not be created'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exporter = new Exporter($this->output_path, $this->date_format);

        $lists = $input->getArgument('lists');

        // filter lists
        if ($lists) {
            $this->lists = $this->getLists($lists);
        }

        $allEntities = [];
        foreach ($this->lists as $list) {
            $this->validateInfo($list);

            //get instance
            $classWithNS = $this->controller_namespace.'\\'.$list['class'];
            $parser = new $classWithNS();

            // set the location of the sanction file
            $parser->setSourcePath($this->getFileListPath($list));

            if ($parser->run()) {
                // export json
                $exporter->json($parser->getEntities(), $list['filename']);

                // export serialize
                if (!$input->getOption('disable-serialize')) {
                    $exporter->serialize($parser->getEntities(), $list['filename']);
                }

                // keep the entities to save the combined file
                $allEntities[] = $parser->getEntities();
            } else {
                echo 'Error processing the parser '.get_class($parser)."\n";
            }

            unset($parser);
        }

        if (!$input->getOption('disable-all')) {
            $exporter->json($allEntities, 'all');
        }

        $output->writeln(self::FINISH_OK);
    }

    /**
     * Get the lists to process.
     *
     * @param $selectedLists
     *
     * @return array
     */
    protected function getLists($selectedLists)
    {
        $listsToProcess = [];
        foreach ($this->lists as $list) {
            if (in_array($list['id'], $selectedLists)) {
                $listsToProcess[] = $list;
            }
        }

        return $listsToProcess;
    }

    /**
     * Validate if the list contains the minimun elemenents.
     *
     * @param $list
     *
     * @throws LogicException
     */
    protected function validateInfo($list)
    {
        if (!array_key_exists('id', $list) || !array_key_exists('url', $list) || !array_key_exists('folder', $list) || !array_key_exists('filename', $list) || !array_key_exists('format', $list) || !array_key_exists('class', $list)) {
            throw new LogicException('Please fill the info of the list');
        }
    }

    /**
     * Get the path where are located the lists.
     *
     * @param array $list
     *
     * @return string path
     */
    protected function getFileListPath($list)
    {
        return $this->list_path.strtolower($list['id']).DIRECTORY_SEPARATOR.strtolower($list['id']).'.'.$list['format'];
    }
}
