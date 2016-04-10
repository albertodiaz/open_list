<?php

namespace ADiaz\AML\OpenList\helpers;

/**
 * Class UnscCreator
 * This class helps to create a similar Unsc lists to be used for tests based in the last version of the list.
 *
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
class UnscCreator
{
    /**
     * Get the file, replace the content and create the content.
     */
    public static function createUnscFixtures()
    {
        $sanctionFileContent = simplexml_load_string(file_get_contents('https://www.un.org/sc/suborg/sites/www.un.org.sc.suborg/files/consolidated.xml'));

        $individuals = $sanctionFileContent->xpath('//INDIVIDUAL');

        foreach ($individuals as $individual) {
            self::replaceContent($individual);
        }

        $entities = $sanctionFileContent->xpath('//ENTITY');

        foreach ($entities as $entity) {
            self::replaceContent($entity);
        }

        $sanctionFileContent->asXML(__DIR__.'/../../../tests/OpenList/fixtures/lists/unsc'.date('_Y-m-d').'.xml');
    }

    /**
     * Replace the content of the node.
     *
     * @param $node
     */
    protected static function replaceContent($node)
    {
        self::setter($node, 'FIRST_NAME', Faker::getName());
        self::setter($node, 'SECOND_NAME', Faker::getSurname());
        self::setter($node, 'THIRD_NAME', Faker::getSurname());
        self::setter($node, 'FOURTH_NAME', Faker::getSurname());
        self::setter($node, 'NAME_ORIGINAL_SCRIPT', Faker::getName());
        self::setter($node, 'COMMENTS1', Faker::getString(20));
        self::setter($node, 'LISTED_ON', Faker::getDate());
        self::setter($node, 'REFERENCE_NUMBER', mt_rand(1000000, 9999999));
        self::setter($node, 'DATAID', mt_rand(10000, 99999));
        self::setter($node, 'VERSIONNUM', mt_rand(1, 99));
        self::setter($node, 'UN_LIST_TYPE', Faker::getCountry());
    }

    /**
     * set the attribute of the node only if exists.
     *
     * @param $node array
     * @param $property string
     * @param $value string|int
     */
    protected static function setter($node, $property, $value)
    {
        if (property_exists($node, $property)) {
            $node->$property = $value;
        }
    }
}

UnscCreator::createUnscFixtures();
