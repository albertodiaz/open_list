<?php

namespace ADiaz\AML\OpenList\models;

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

/**
 * Class Entity.
 */
class Entity
{
    public $external_id;
    public $first_name;
    public $last_name;
    public $name_original_script;
    public $type;
    public $title;
    public $position;
    public $program_list;
    public $aka_list;
    public $address_list;
    public $id_list;
    public $nationality_list;
    public $citizenship_list;
    public $date_of_birth_list;
    public $place_of_birth_list;
    public $vessel;
    public $remarks;
    //@TODO add update_at_list

    /* Individual */
    const TYPE_INDIVIDUAL = 'Individual';

    /* Entity / Company */
    const TYPE_ENTITY = 'Entity';

    /* prime alias */
    const ALIAS_TYPE_PRIME_ALIAS = 'Prime Alias';

    /* also known as */
    const ALIAS_TYPE_AKA = 'AKA';

    /* formerly known as */
    const ALIAS_TYPE_FKA = 'FKA';

    public function __construct($external_id)
    {
        $this->id_list = [];
        $this->aka_list = [];
        $this->address_list = [];
        $this->program_list = [];
        $this->address_list = [];
        $this->nationality_list = [];
        $this->citizenship_list = [];
        $this->date_of_birth_list = [];
        $this->place_of_birth_list = [];
        $this->external_id = $external_id;
    }

    public function addAka(Aka $aka)
    {
        if (!$this->isEmpty($aka) && !in_array($aka, $this->aka_list)) {
            $this->aka_list[] = $aka;
        }
    }

    public function addId(Id $id)
    {
        if (!$this->isEmpty($id) && !in_array($id, $this->id_list)) {
            $this->id_list[] = $id;
        }
    }

    public function addNationality(Nationality $nationality)
    {
        if (!$this->isEmpty($nationality) && !in_array($nationality, $this->nationality_list)) {
            $this->nationality_list[] = $nationality;
        }
    }

    public function addCitizenship(Citizenship $citizenship)
    {
        if (!$this->isEmpty($citizenship) && !in_array($citizenship, $this->citizenship_list)) {
            $this->citizenship_list[] = $citizenship;
        }
    }

    public function addAddress(Address $address)
    {
        if (!$this->isEmpty($address) && !in_array($address, $this->address_list)) {
            $this->address_list[] = $address;
        }
    }

    public function addDateOfBirth(DateOfBirth $dob)
    {
        if (!$this->isEmpty($dob) && !in_array($dob, $this->date_of_birth_list)) {
            $this->date_of_birth_list[] = $dob;
        }
    }

    public function addPlaceOfBirth(PlaceOfBirth $pob)
    {
        if (!$this->isEmpty($pob) && !in_array($pob, $this->place_of_birth_list)) {
            $this->place_of_birth_list[] = $pob;
        }
    }

    public function addProgram(Program $program)
    {
        if (!$this->isEmpty($program) && !in_array($program, $this->program_list)) {
            $this->program_list[] = $program;
        }
    }

    public function isEmpty($obj)
    {
        foreach ($obj as $key => $value) {
            if ((is_string($value) && trim($value) !== '')) {
                return false;
            }
        }

        return true;
    }

    public function setter($attribute, $value)
    {
        if (property_exists($this, $attribute) && !empty($value)) {
            $this->$attribute = $value;
        }
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
