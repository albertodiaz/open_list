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
class DateOfBirth
{
    public $id;
    public $date_of_birth;
    public $main_entry;

    /**
     * @param $date
     * @param bool $format
     */
    public function setDateOfBirth($date, $format = false)
    {
        if ($format) {
            $myDateTime = \DateTime::createFromFormat('d/m/Y', $date);
            $this->date_of_birth = $myDateTime->format('Y-m-d');
        } else {
            $this->date_of_birth = $date;
        }
    }
}
