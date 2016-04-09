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
class Address
{
    public $id;
    public $address1;
    public $address2;
    public $address3;
    public $city;
    public $state_or_province;
    public $postal_code;
    public $country;
    //Todo add @note or something similar
}
