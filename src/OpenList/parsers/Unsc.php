<?php

namespace ADiaz\AML\OpenList\parsers;

use ADiaz\AML\OpenList\models\Entity;
use ADiaz\AML\OpenList\models\Address;
use ADiaz\AML\OpenList\models\Aka;
use ADiaz\AML\OpenList\models\DateOfBirth;
use ADiaz\AML\OpenList\models\Id;
use ADiaz\AML\OpenList\models\Nationality;
use ADiaz\AML\OpenList\models\PlaceOfBirth;
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
class Unsc implements IList
{
    protected $path_source;
    protected $sanction_file_content;
    protected $entities;
    protected $date;

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
    public function run()
    {
        if ($this->readEntities()) {
            $this->processEntities();
            $this->setDate();

            return $this->verifyEntities();
        }
    }

    /**
     * Set source path, the file path of the list.
     * this method will be called by the Processor.
     *
     * Suggestion: add a property to the class
     *
     * @param string $path
     */
    public function setSourcePath($path)
    {
        $this->path_source = $path;
    }

    /**
     * Read the content from the file list.
     *
     * Suggestion: add a property to the class to save the content
     *
     * @TODO change the name to setFileContent;
     */
    public function readEntities()
    {
        $this->sanction_file_content = simplexml_load_string(file_get_contents($this->path_source));

        return $this->sanction_file_content !== false;
    }

    /**
     * Process entities. It analyses the entities and it parses to classes.
     */
    public function processEntities()
    {
        $individuals = $this->sanction_file_content->xpath('//INDIVIDUAL');

        foreach ($individuals as $individual) {
            $this->entities[] = $this->parseIndividuals($individual);
        }

        $entities = $this->sanction_file_content->xpath('//ENTITY');

        foreach ($entities as $entity) {
            $this->entities[] = $this->parseEntitiesAndOtherGroups($entity);
        }
    }

    /**
     * Parse the individuals.
     *
     * @param \SimpleXMLElement $node
     *
     * @return Entity
     */
    protected function parseIndividuals($node)
    {
        $entity = new Entity($this->g($node->xpath('DATAID')));
        $entity->type = $entity::TYPE_INDIVIDUAL;
        $entity->first_name = $this->g($node->xpath('FIRST_NAME'));
        $entity->last_name = $this->combine([$this->g($node->xpath('SECOND_NAME')), $this->g($node->xpath('THIRD_NAME')), $this->g($node->xpath('FOURTH_NAME'))]);
        $entity->name_original_script = $this->g($node->xpath('NAME_ORIGINAL_SCRIPT'));
        $entity->remarks = $this->g($node->xpath('COMMENTS1'));
        $entity->position = $this->parseDesignations($node->xpath('DESIGNATION'));

        $entity->program_list = $this->parsePrograms($node->xpath('UN_LIST_TYPE'));
        $entity->aka_list = $this->parseAkas($node->xpath('INDIVIDUAL_ALIAS'));
        $entity->address_list = $this->parseAddresses($node->xpath('INDIVIDUAL_ADDRESS'));
        $entity->date_of_birth_list = $this->parseDateOfBirth($node->xpath('INDIVIDUAL_DATE_OF_BIRTH'));
        $entity->place_of_birth_list = $this->parsePlaceOfBirth($node->xpath('INDIVIDUAL_PLACE_OF_BIRTH'));
        $entity->date_of_birth_list = $this->parseIds($node->xpath('INDIVIDUAL_DOCUMENT'));

        return $entity;
    }

    /**
     * Parse the entities.
     *
     * @param \SimpleXMLElement $node
     *
     * @return Entity Entity
     */
    protected function parseEntitiesAndOtherGroups($node)
    {
        $entity = new Entity($this->g($node->xpath('DATAID')));
        $entity->type = $entity::TYPE_ENTITY;
        $entity->first_name = $this->g($node->xpath('FIRST_NAME'));
        //@Todo <xs:element ref="REFERENCE_NUMBER"/>
        //@Todo <xs:element ref="LISTED_ON"/>
        //@Todo <xs:element minOccurs="0" ref="SUBMITTED_ON"/>
        $entity->name_original_script = $this->g($node->xpath('NAME_ORIGINAL_SCRIPT'));
        $entity->remarks = $this->g($node->xpath('COMMENTS1'));
        $entity->program_list = $this->parsePrograms($node->xpath('UN_LIST_TYPE'));
        //@Todo <xs:element minOccurs="0" ref="LAST_DAY_UPDATED"/>

        $entity->aka_list = $this->parseAkas($node->xpath('ENTITY_ALIAS'));
        $entity->address_list = $this->parseAddresses($node->xpath('ENTITY_ADDRESS'));
        //@Todo <xs:element ref="SORT_KEY"/>
        //@Todo <xs:element ref="SORT_KEY_LAST_MOD"/>
        //@Todo <xs:element minOccurs="0" ref="DELISTED_ON"/>

        return $entity;
    }

    /**
     * Parse the alias of a node.
     *
     * @param array $alias
     *
     * @return array list of akas
     */
    protected function parseAkas($alias)
    {
        $akas = [];
        foreach ($alias as $alia) {
            //it can be a name, or an string with multiple names separated by ;
            $names = explode(';', $this->g($alia->xpath('ALIAS_NAME')));
            foreach ($names as $name) {
                $aka = new Aka();
                //@Todo it should be full_name
                $aka->first_name = trim($name);
                //@TODO it should be save in quality
                $aka->category = $this->g($alia->xpath('QUALITY'));
                //@TODO Missing to parse DATE_OF_BIRTH CITY_OF_BIRTH COUNTRY_OF_BIRTH NOTE

                $akas[] = $aka;
            }
        }

        return $akas;
    }

    /**
     * Parse the programs.
     *
     * @param $listTypes
     *
     * @return array
     */
    protected function parsePrograms($listTypes)
    {
        $programs = [];
        foreach ($listTypes as $listType) {
            if ($listType->count()) {
                $program = new Program();
                $program->program = $listType;
                $programs[] = $program;
            }
        }

        return $programs;
    }

    /**
     * Parses the addresses of a node.
     *
     * @param \SimpleXMLElement $addresses array
     *
     * @return array list of addresses
     */
    protected function parseAddresses($addresses)
    {
        $locations = [];
        foreach ($addresses as $address) {
            // check if empty the SimpleXmlElement
            if ($address->count()) {
                $location = new Address();
                $location->country = $this->g($address->xpath('COUNTRY'));
                $location->address1 = $this->g($address->xpath('STREET'));
                $location->city = $this->g($address->xpath('CITY'));
                $location->postal_code = $this->g($address->xpath('ZIP_CODE'));
                $location->state_or_province = $this->g($address->xpath('STATE_PROVINCE'));
                //@Todo it should be saved the note in Remarks or better in address
                //$location->note = $this->g($address->xpath('NOTE'));

                $locations[] = $location;
            }
        }

        return $locations;
    }

    /**
     * Parse the date of births of a node.
     *
     * @param \SimpleXMLElement $dobs
     *
     * @return array list of date of birth
     */
    protected function parseDateOfBirth($dobs)
    {
        $dateOfBirths = [];

        if ($this->areElements($dobs)) {
            foreach ($dobs as $dob) {
                $date = $this->g($dob->xpath('DATE'));

                if ($date !== null) {
                    $dateOfBirth = new DateOfBirth();
                    $dateOfBirth->date_of_birth = $date;
                    $dateOfBirths[] = $dateOfBirth;
                }

                $year = $this->g($dob->xpath('YEAR'));
                if ($year !== null) {
                    $dateOfBirth = new DateOfBirth();
                    $dateOfBirth->date_of_birth = $year;
                    $dateOfBirths[] = $dateOfBirth;
                }

                $between = $this->g($dob->xpath('FROM_YEAR')).'-'.$this->g($dob->xpath('TO_YEAR'));
                if ($between !== '-') {
                    $dateOfBirth = new DateOfBirth();
                    $dateOfBirth->date_of_birth = $between;
                    $dateOfBirths[] = $dateOfBirth;
                }

                //@Todo it should be saved the note in Remarks or better in address
            }
        }

        return $dateOfBirths;
    }

    /**
     * Parse place of birth.
     *
     * @param \SimpleXMLElement $places_of_birth
     *
     * @return array list of place of birth
     */
    protected function parsePlaceOfBirth($places_of_birth)
    {
        $locations = [];
        foreach ($places_of_birth as $placeOfBirth) {
            if ($placeOfBirth->count()) {
                $location = new PlaceOfBirth();
                $location->place_of_birth = $this->g($placeOfBirth->xpath('CITY'));
                $location->state_province = $this->g($placeOfBirth->xpath('STATE_PROVINCE'));
                $location->country = $this->g($placeOfBirth->xpath('COUNTRY'));
                $location->remarks = $this->g($placeOfBirth->xpath('NOTE'));

                $locations[] = $location;
            }
        }

        return $locations;
    }

    /**
     * Parse individual documents.
     *
     * @param \SimpleXMLElement $documents
     *
     * @return array list of the individual documents
     */
    protected function parseIds($documents)
    {
        $ids = [];
        foreach ($documents as $document) {
            $id = new Id();
            $id->country = $this->g($document->xpath('COUNTRY_OF_ISSUE'));
            $id->date = $this->g($document->xpath('DATE_OF_ISSUE'));
            $id->number = $this->g($document->xpath('NUMBER'));
            $id->type = $this->g($document->xpath('TYPE_OF_DOCUMENT'));
            $id->remarks .= $this->addToRemarks($this->g($document->xpath('TYPE_OF_DOCUMENT2')), 'TYPE_OF_DOCUMENT2');
            $id->remarks .= $this->addToRemarks($this->g($document->xpath('CITY_OF_ISSUE')), 'CITY_OF_ISSUE');
            $id->remarks .= $this->addToRemarks($this->g($document->xpath('ISSUING_COUNTRY')), 'ISSUING_COUNTRY');
            $id->remarks .= $this->addToRemarks($this->g($document->xpath('COUNTRY_OF_ISSUE')), 'COUNTRY_OF_ISSUE');
            $id->remarks .= $this->addToRemarks($this->g($document->xpath('NOTE')), 'NOTE');

            $ids[] = $id;
        }

        return $ids;
    }

    /**
     * Parse nationalities.
     *
     * @param \SimpleXMLElement $node
     *
     * @return array list of nationalities
     */
    protected function parseNationalities($node)
    {
        $nationalities = [];
        $subNodes = $node->xpath('NATIONALITY');
        foreach ($subNodes as $subNode) {
            $nationality = new Nationality();
            $nationality->country = $this->g($subNode->xpath('VALUE'));
            $nationalities[] = $nationality;
        }

        return $nationalities;
    }

    /**
     * Parse designations.
     *
     * @param $subNodes array
     * @param string $delimeter
     *
     * @return string with all designations
     */
    protected function parseDesignations($subNodes, $delimeter = ';')
    {
        $designations = [];
        foreach ($subNodes as $subNode) {
            $designations[] = $this->g($subNode->xpath('VALUE'));
        }

        return implode($delimeter, $designations);
    }

    protected function addToRemarks($str, $label)
    {
        return ($str !== null) ? $label.' '.$str : '';
    }

    /**
     * @param $array
     *
     * @return bool
     * @TODO improve it
     */
    protected function areElements($array)
    {
        return !empty($array) && (json_encode($array) !== '[{}]');
    }

    /**
     * Combine an array in a string seperate by an space
     * At the end it replaces multiple spaces with a single space.
     *
     * @param $array array
     *
     * @return string
     */
    protected function combine($array)
    {
        return trim(preg_replace('!\s+!', ' ', implode(' ', $array)));
    }

    /**
     * It is just a helper to extract the first element from
     * an array and transform it to string.
     *
     * @param $array
     *
     * @return null|string
     */
    protected function g($array)
    {
        return isset($array[0]) ? (string) $array[0] : null;
    }

    /**
     * Verify the entities, for example the content or
     * if the amount of entities match with amount from the file if it is provided.
     */
    public function verifyEntities()
    {
        return true;
    }

    /**
     * Get entities
     * return the entities created.
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Set the date when the list was updated, it takes the info from the content of the file if exists.
     *
     * @TODO add an attribute to send the format of the date
     */
    public function setDate()
    {
        /* Extract from dateGenerated
        <CONSOLIDATED_LIST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="http://www.un.org/sc/committees/resources/xsd/sc-sanctions.xsd"
        dateGenerated="2016-03-07T13:06:25.399-05:00">
         **/
    }

    /**
     * Get the date of the list. When it was updated.
     * It must be given in Y-m-d format.
     */
    public function getDate()
    {
        return $this->date;
    }
}
