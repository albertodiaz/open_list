<?php

namespace ADiaz\AML\OpenList\parsers;

use ADiaz\AML\OpenList\models\Entity;
use ADiaz\AML\OpenList\models\Address;
use ADiaz\AML\OpenList\models\Aka;
use ADiaz\AML\OpenList\models\Citizenship;
use ADiaz\AML\OpenList\models\DateOfBirth;
use ADiaz\AML\OpenList\models\Id;
use ADiaz\AML\OpenList\models\Nationality;
use ADiaz\AML\OpenList\models\PlaceOfBirth;
use ADiaz\AML\OpenList\models\Vessel;
use ADiaz\AML\OpenList\models\Program;

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
class Usofac implements IList
{
    const ID = 'Id';
    const AKA = 'Aka';
    const NATIONALITY = 'Nationality';
    const ADDRESS = 'Address';
    const CITIZENSHIP = 'Citizenship';
    const DATE_OF_BIRTH = 'DateOfBirth';
    const PLACE_OF_BIRTH = 'PlaceOfBirth';
    const VESSEL = 'Vessel';
    const PROGRAM = 'Program';

    const ERROR_FILE = 'Error. There was problem loading the file';
    const ERROR_TOTAL_ENTITIES = 'Error. The number of entities doesn\'t math';

    public $path_source;
    public $sanction_file_content;
    public $sanctions;
    public $entities;
    public $date;

    public function setSourcePath($pathSource)
    {
        $this->path_source = $pathSource;
    }

    public function run()
    {
        if ($this->readEntities()) {
            $this->processEntities();
            $this->setDate();

            return $this->verifyEntities();
        }
        // Log self:ERROR_FILE
    }

    public function readEntities()
    {
        $xml = simplexml_load_string(file_get_contents($this->path_source));

        $this->sanction_file_content = $this->xmlToArray($xml);

        return !empty($this->sanction_file_content);
    }

    /**
     * Convert xml to array.
     *
     * @param string $xml
     *
     * @return array
     */
    protected function xmlToArray($xml)
    {
        $json = json_encode($xml);

        return $this->sanction_file_content = json_decode($json, true);
    }

    public function verifyEntities()
    {
        $result = (count($this->entities) === (int) $this->sanction_file_content['publshInformation']['Record_Count']);
        if (!$result) {
            //log
            echo self::ERROR_TOTAL_ENTITIES.'Detail: total entities '.count($this->entities).', records in the list: '.(int) $this->sanction_file_content['publshInformation']['Record_Count'];
        }

        return $result;
    }

    protected function getMapper()
    {
        return [
            self::ID => [
                'id' => 'uid',
                'type' => 'idType',
                'number' => 'idNumber',
                'country' => 'idCountry',
                'date' => 'issueDate',
                'expirationDate' => 'expirationDate',
            ],
            self::AKA => [
                'id' => 'uid',
                'type' => 'idType',
                'category' => 'category',
                'last_name' => 'lastName',
                'first_name' => 'firstName',
            ],
            self::ADDRESS => [
                'id' => 'uid',
                'address1' => 'address1',
                'address2' => 'address2',
                'address3' => 'address3',
                'city' => 'city',
                'state_or_province' => 'stateOrProvince',
                'postal_code' => 'postalCode',
                'country' => 'country',
            ],
            self::NATIONALITY => [
                'id' => 'uid',
                'country' => 'country',
                'main_entry' => 'mainEntry',
            ],
            self::CITIZENSHIP => [
                'id' => 'uid',
                'country' => 'country',
                'main_entry' => 'mainEntry',
            ],
            self::DATE_OF_BIRTH => [
                'id' => 'uid',
                'date_of_birth' => 'dateOfBirth',
                'main_entry' => 'mainEntry',
            ],
            self::PLACE_OF_BIRTH => [
                'id' => 'uid',
                'place_of_birth' => 'placeOfBirth',
                'main_entry' => 'mainEntry',
            ],
            self::VESSEL => [
                'call_sign' => 'callSign',
                'vessel_type' => 'vesselType',
                'vessel_flag' => 'vesselFlag',
                'vessel_owner' => 'vesselOwner',
                'tonnage' => 'tonnage',
                'gross_registered_tonnage' => 'grossRegisteredTonnage',
            ],
            self::PROGRAM => [
                'program' => 'program',
            ],
        ];
    }

    /**
     * Create the entities and set them.
     */
    public function processEntities()
    {
        $this->sanctions = $this->sanction_file_content['sdnEntry'];

        foreach ($this->sanctions as $s) {
            $entity = new Entity($s['uid']);

            $entity->first_name = isset($s['firstName']) ? $s['firstName'] : null;
            $entity->last_name = isset($s['lastName']) ? $s['lastName'] : null;
            $entity->title = isset($s['title']) ? $s['title'] : null;
            $entity->type = isset($s['sdnType']) ? $s['sdnType'] : null;
            $entity->remarks = isset($s['remarks']) ? $s['remarks'] : null;

            $entity->program_list = $this->getList($s, 'programList', self::PROGRAM);
            $entity->citizenship_list = $this->getList($s, 'citizenship', self::CITIZENSHIP, 'citizenshipList');
            $entity->aka_list = $this->getList($s, 'aka', self::AKA, 'akaList');
            $entity->address_list = $this->getList($s, 'address', self::ADDRESS, 'addressList');
            $entity->id_list = $this->getList($s, 'id', self::ID, 'idList');
            $entity->nationality_list = $this->getList($s, 'nationality', self::NATIONALITY, 'nationalityList');
            $entity->date_of_birth_list = $this->getList($s, 'dateOfBirthItem', self::DATE_OF_BIRTH, 'dateOfBirthList');
            $entity->place_of_birth_list = $this->getList($s, 'placeOfBirthItem', self::PLACE_OF_BIRTH, 'placeOfBirthList');
            $entity->vessel = $this->getList($s, 'vesselInfo', self::VESSEL);

            $this->entities[] = $entity;
        }
    }

    /**
     * get the content of the list.
     *
     * @param array       $entityNode
     * @param string      $node
     * @param string      $className
     * @param bool|string $listName
     *
     * @return array
     */
    protected function getList(array $entityNode, $node, $className, $listName = false)
    {
        $mapper = $this->getMapper()[$className];
        $list = $this->getListContent($entityNode, $listName, $node);

        if (!$this->hasSubArrays($list)) {
            return [$this->createSubsIntance($list, $className, $mapper)];
        } else {
            $elements = [];
            foreach ($list as $node) {
                $elements[] = $this->createSubsIntance($node, $className, $mapper);
            }

            return $elements;
        }
    }

    /**
     * Check if an array has sub arrays.
     *
     * @param array $array
     *
     * @return bool
     */
    protected function hasSubArrays(array $array)
    {
        return is_array($array) && count($array, COUNT_RECURSIVE) !== count($array);
    }

    /**
     * Get the content of a subArray up two levels.
     *
     * @param array|null  $entityNode
     * @param bool|string $listName
     * @param string      $node
     *
     * @return array
     */
    protected function getListContent($entityNode, $listName, $node)
    {
        $content = false;

        if (isset($entityNode[$listName][$node])) {
            $content = $entityNode[$listName][$node];
        } elseif (isset($entityNode[$node])) {
            $content = $entityNode[$node];
        }

        return $content ? $content : array();
    }

    /**
     * Based of the subnode, creates an instance and set the attributes.
     *
     * @param array  $node      info to get
     * @param string $className class to generate
     * @param array  $mapper
     *
     * @return mixed
     */
    protected function createSubsIntance(array $node, $className, array $mapper)
    {
        // if the subnode exists (for example program)
        if (isset($node)) {
            // get a new instance
            $classWithNS = 'ADiaz\AML\OpenList\models\\'.$className;

            $instance = new $classWithNS();

            // set the attributes based of the mapper
            foreach ($mapper as $key => $value) {
                $instance->$key = isset($node[$value]) ? $node[$value] : null;
            }

            return $instance;
        }
    }

    public function setDate()
    {
        $this->date = $this->sanction_file_content['publshInformation']['Publish_Date'];
    }

    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Get the date of the list. When it was updated.
     */
    public function getDate()
    {
        return $this->date;
    }
}
