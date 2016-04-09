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
class Ukhmt implements IList
{
    protected $prime_alias;
    protected $aka_fka_rows;
    protected $list_file_content;
    protected $path_source;
    protected $entities = [];
    protected $date;

    const ERROR_DIVIDING_ROWS = 'Error dividing the rows.';
    const ERROR_FILE = 'Error. The sanction list was not found. Have you executed the downloader?.';
    const ERROR_VERIFYING_ENTITIES = 'Error. The sanction list was not found. Please verify if it was downloaded and the list configuration file.';

    /**
     * Add the entity if it doesn't exsist already in teh array of entities
     * if it exists it does nothing.
     *
     * @param Entity $entity
     *
     * @return null|bool if the entity is added to the array
     */
    protected function addEntity(Entity $entity)
    {
        foreach ($this->entities as $ent) {
            if ($ent->external_id === $entity->external_id) {
                return;
            }
        }

        $this->entities [] = $entity;

        return true;
    }

    /**
     * Get the value from one or multiple cols.
     *
     * @param string $value
     * @param array  $cols
     *
     * @return string|null
     */
    protected function getCols($value, $cols)
    {
        $mValue = $this->getMap()[$value];

        if (is_array($mValue)) {
            $str = '';
            foreach ($mValue as $subIdMapper) {
                $str .= $cols[$subIdMapper].' ';
            }
        } else {
            $str = $cols[$mValue];
        }

        $strNoExtraSpaces = trim(preg_replace('/\s+/', ' ', $str));

        return ($strNoExtraSpaces !== '') ? $strNoExtraSpaces : null;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Get the ids of the entities.
     *
     * @return array
     */
    protected function getEntitiesIds()
    {
        $entitiesIds = [];

        foreach ($this->entities as $entity) {
            $entitiesIds[] = $entity->external_id;
        }

        return $entitiesIds;
    }

    /**
     * Get a new entity if it doesn't exist, or get the entity created
     * if already exists.
     *
     * @param int $id
     *
     * @return Entity
     */
    protected function getEntity($id)
    {
        foreach ($this->entities as $entity) {
            if ($entity->external_id === $id) {
                return $entity;
            }
        }

        return new Entity($id);
    }

    /**
     * Get the ids of the list.
     *
     * @return array
     */
    protected function getGroupsIdsFromSource()
    {
        $group_ids = [];
        $i = 0;

        // iterate over each line
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $this->list_file_content) as $line) {
            $cols = str_getcsv($line);

            if ($i > 1) {
                $group_id = $cols[$this->getMap()['group_id']];

                $group_ids [$group_id] = $group_id;
            }
            ++$i;
        }

        return $group_ids;
    }

    /**
     * Return the mappers the fields with the columns of the excel document.
     *
     * @return array with the mapping
     */
    protected function getMap()
    {
        $fields = [
            'last_name' => 0,
            'first_name' => [1, 2, 3, 4, 5],
            'title' => 6,
            'dob' => 7,
            'tob' => 8,
            'cob' => 9,
            'nationality' => 10,
            'passport' => 11,
            'ni_number' => 12,
            'position' => 13,
            'address' => [14, 15, 16, 17, 18],
            'city' => 19,
            'postcode' => 20,
            'country' => 21,
            'other_information' => 22,
            'group_type' => 23,
            'row_type' => 24,
            'regime' => 25,
            'listen_on' => 26,
            'last_updated' => 27,
            'group_id' => 28,
        ];

        return $fields;
    }

    /**
     * Check if if a row (after str_getcsv) is prime alias.
     *
     * @param array $cols
     *
     * @return bool true if yes
     */
    protected function isPrimeAlias(array $cols)
    {
        return $cols[$this->getMap()['row_type']] === Entity::ALIAS_TYPE_PRIME_ALIAS;
    }

    /**
     * Check if if a row (after str_getcsv) is an AKA.
     *
     * @param array $cols
     *
     * @return bool true if yes
     */
    protected function isAka(array $cols)
    {
        return $cols[$this->getMap()['row_type']] === Entity::ALIAS_TYPE_AKA;
    }

    /**
     * Check if if a row (after str_getcsv) is an FKA.
     *
     * @param array $cols
     *
     * @return bool true if yes
     */
    protected function isFka(array $cols)
    {
        return $cols[$this->getMap()['row_type']] === Entity::ALIAS_TYPE_FKA;
    }

    /**
     * A separate record is provided for each permutation of data involving “Name”, “Date of Birth”,
     *   “Address” and “Regime”.
     */
    protected function processAkasAndFkas()
    {
        foreach ($this->aka_fka_rows as $row) {
            $cols = str_getcsv($row);

            // get the entity to fill it with info
            $entity = $this->getEntity($this->getCols('group_id', $cols));

            //add aka
            $aka = new Aka();
            $aka->last_name = $this->getCols('last_name', $cols);
            $aka->first_name = $this->getCols('first_name', $cols);
            $entity->addAka($aka);

            // add dob
            $entity->addDateOfBirth(new DateOfBirth($this->getCols('dob', $cols)));

            // add address
            $address = new Address();
            $address->address1 = $this->getCols('address', $cols);
            $address->city = $this->getCols('city', $cols);
            $address->country = $this->getCols('country', $cols);
            $address->postal_code = $this->getCols('postcode', $cols);
            $entity->addAddress($address);

            // add program
            $entity->addProgram(new Program($this->getCols('regime', $cols)));
        }
    }

    public function processEntities()
    {
        $this->processPrimeAlias();
        $this->processAkasAndFkas();
    }

    public function run()
    {
        $this->readEntities();
        $this->processEntities();
        $this->setDate();

        return $this->verifyEntities();
        //log die(self::ERROR_VERIFYING_ENTITIES);
    }

    /**
     * Process entity prime alias.
     */
    protected function processPrimeAlias()
    {
        foreach ($this->prime_alias as $row) {
            $cols = str_getcsv($row);

            $entity = $this->getEntity($this->getCols('group_id', $cols));

            $entity->setter('last_name', $this->getCols('last_name', $cols));
            $entity->setter('first_name', $this->getCols('first_name', $cols));
            $entity->setter('title', $this->getCols('title', $cols));
            $entity->setType($this->getCols('group_type', $cols));
            $entity->setter('position', $this->getCols('position', $cols));
            $entity->setter('remarks', $this->getCols('other_information', $cols));

            // add address
            $address = new Address();
            $address->address1 = $this->getCols('address', $cols);
            $address->city = $this->getCols('city', $cols);
            $address->country = $this->getCols('country', $cols);
            $address->postal_code = $this->getCols('postcode', $cols);
            $entity->addAddress($address);

            // add dob
            $dateOfBirth = new DateOfBirth();
            $dateOfBirth->date_of_birth = $this->getCols('dob', $cols);
            $entity->addDateOfBirth($dateOfBirth);

            // add POB town of birth
            $placeOfBirth = new PlaceOfBirth();
            $placeOfBirth->place_of_birth = $this->getCols('tob', $cols);
            $entity->addPlaceOfBirth($placeOfBirth);

            // add COB country of birth to the place of birth
            $countryOfBirth = new PlaceOfBirth();
            $countryOfBirth->place_of_birth = $this->getCols('cob', $cols);
            $entity->addPlaceOfBirth($countryOfBirth);

            // add program
            $program = new Program();
            $program->program = $this->getCols('regime', $cols);
            $entity->addProgram($program);

            // add citizenship
            $citizenship = new Citizenship();
            $citizenship->country = $this->getCols('nationality', $cols);
            $entity->addCitizenship($citizenship);

            // add nationality
            $nationality = new Nationality();
            $nationality->country = $this->getCols('nationality', $cols);
            $entity->addNationality($nationality);

            // add Passport number(s) - where issued, issued/expiry dates
            $passport = new Id();
            $passport->type = Id::TYPE_PASSPORT;
            $passport->mixed = $this->getCols('passport', $cols);
            $entity->addId($passport);

            // add ids (ssn, national ids, passports)
            $nid = new Id();
            $nid->type = Id::TYPE_NATIONAL_ID_OR_SSN;
            $nid->mixed = $this->getCols('nationality', $cols);
            $entity->addId($nid);

            $this->addEntity($entity);
        }
    }

    public function readEntities()
    {
        // change encoding, review it in the future since https://www.gov.uk/government/publications/open-standards-for-government/cross-platform-character-encoding-profile
        $this->list_file_content = utf8_encode(file_get_contents($this->path_source));

        // check the file
        if (!$this->list_file_content) {
            die(self::ERROR_FILE);
        }

        $this->setPrimaryEntitiesAkasFkas();
    }

    public function setDate()
    {
        $lines = preg_split("/((\r?\n)|(\r\n?))/", $this->list_file_content);

        $dateLine = str_getcsv($lines[0]);

        $this->date = $dateLine[1];
    }

    /**
     * Divide the entities in primary, akas and fkas.
     */
    protected function setPrimaryEntitiesAkasFkas()
    {
        $firstLine = true;

        // iterate over each line
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $this->list_file_content) as $line) {
            $cols = str_getcsv($line);

            if ($firstLine) {
                $firstLine = false;
                continue;
            }

            if ($this->isPrimeAlias($cols)) {
                $this->prime_alias[] = $line;
            } elseif ($this->isAka($cols) || $this->isFka($cols)) {
                $this->aka_fka_rows[] = $line;
            }
        }
    }

    public function setSourcePath($pathSource)
    {
        $this->path_source = $pathSource;
    }

    /**
     * Check the number of entities math with the total of entities of the document.
     */
    public function verifyEntities()
    {
        return $this->verifyIds();
    }

    /**
     * Check if the ids of the entities are the same that the ids from the list file.
     *
     * @return bool
     */
    protected function verifyIds()
    {
        return count(array_diff($this->getGroupsIdsFromSource(), $this->getEntitiesIds())) == 0
        && count($this->getEntitiesIds()) > 0;
    }
}
