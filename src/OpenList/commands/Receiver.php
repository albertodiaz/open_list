<?php

namespace ADiaz\AML\OpenList\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ADiaz\AML\OpenList\Utils\Utils;

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
class Receiver extends Command
{
    const FINISH_OK = 'The lists were downloaded';

    protected $list_path;
    protected $lists;
    protected $date_format;

    /**
     * Receiver constructor.
     *
     * @param null|string $conf
     */
    public function __construct($conf)
    {
        $this->list_path = $conf['listsPath'];
        $this->date_format = $conf['dateFormat'];
        $this->lists = (array) $conf['lists'];

        parent::__construct();
    }

    /**
     * Configure the arguments and options.
     */
    protected function configure()
    {
        $this
            ->setName('receive')
            ->setDescription('Receive the lists')
            ->addArgument(
                'lists',
                InputArgument::IS_ARRAY,
                'Which lists do you want to process (separate multiple names with a space)?'
            );
    }

    /**
     * Get the lists.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lists = $input->getArgument('lists');

        if ($lists) {
            $this->lists = $this->getLists($lists);
        }

        $this->createFolders($this->lists);

        $this->archive($this->lists);

        $this->downloadFiles($this->lists);

        $this->verifyFiles($this->lists);

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
     * It creates the folders for the lists if they don't exist already.
     *
     * @param array $listsInfo
     */
    protected function createFolders(array $listsInfo)
    {
        foreach ($listsInfo as $list) {
            if (is_dir($this->list_path.$list['folder']) === false) {
                mkdir($this->list_path.$list['folder']);
            }
        }
    }

    /**
     * Archives the existing lists.
     *
     * @param array $listsInfo
     */
    protected function archive($listsInfo)
    {
        foreach ($listsInfo as $list) {
            if (file_exists($this->getFilePath($list))) {
                //compress
                Utils::gzCompressFile($this->getFilePath($list), $this->getArchiveName($this->getFilePath($list)));
                //delete
                unlink($this->getFilePath($list));
            }
        }
    }

    /**
     * Downloads the lists.
     *
     * @param array $listsInfo
     * @Todo  Check encoding with mbstring and take a look at http://stackoverflow.com/questions/505562/detect-file-encoding-in-php#answer-505582
     */
    protected function downloadFiles($listsInfo)
    {
        foreach ($listsInfo as $list) {
            file_put_contents($this->getFilePath($list), file_get_contents($list['url']));
        }
    }

    /**
     * Verifies the lists were downloaded and exists.
     *
     * @param array $listsInfo
     */
    protected function verifyFiles($listsInfo)
    {
        foreach ($listsInfo as $list) {
            if (!file_exists($this->getFilePath($list))) {
                echo "The was a problem verifying the list {$list['name']}. The file {$this->getFilePath($list)} doesn't exist";
            }
        }
    }

    /**
     * Gets the archive name from a filepath.
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function getArchiveName($filePath)
    {
        $fileName = basename($filePath);
        $newFileName = date($this->date_format).'_'.$fileName;

        return str_replace($fileName, $newFileName, $filePath);
    }

    /**
     * Get the filename from a list.
     *
     * @param array $list
     *
     * @return string
     */
    protected function getFilename($list)
    {
        return $list['filename'].'.'.$list['format'];
    }

    /**
     * Get the filepath of the list.
     *
     * @param array $list
     *
     * @return string
     */
    protected function getFilePath($list)
    {
        return $this->list_path.$list['folder'].'/'.$this->getFilename($list);
    }
}
