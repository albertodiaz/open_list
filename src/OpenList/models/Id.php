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
class Id
{
    const TYPE_PASSPORT = 'Passport';
    const TYPE_NATIONAL_ID = 'National Identification';
    const TYPE_SSN = 'Social Security Number';
    const TYPE_NATIONAL_ID_OR_SSN = 'National Identification (ID card numbers, Social Security Numbers etc. )';

    public $id;
    public $type;
    public $number;
    public $country;
    public $date;
    public $expiration;
    public $mixed;
    public $remarks;
}
