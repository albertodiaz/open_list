<?php

namespace ADiaz\AML\OpenList\helpers;

/**
 * This file is part of the OpenList Parser utility.
 *
 * Class OfacCreator
 * This class helps to create a similar Unsc lists to be used for tests based in the last version of the list.
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
class OfacCreator
{
    /**
     * Get the file, replace the content and create the content.
     */
    public static function createOfacFixtures()
    {
        $sanctionFileContent = simplexml_load_string(file_get_contents('http://www.treasury.gov/ofac/downloads/sdn.xml'));

        foreach ($sanctionFileContent as $node) {
            self::replaceContent($node);
        }

        $sanctionFileContent->asXML(__DIR__.'/../../../tests/OpenList/fixtures/lists/usofac'.date('_Y-m-d').'.xml');
    }

    /**
     * Replace the content of the node.
     *
     * @param $node
     */
    protected static function replaceContent($node)
    {
        self::setter($node, 'firstName', Faker::getName());
        self::setter($node, 'lastName', Faker::getSurname());
        self::setter($node, 'sdnType', Faker::randomElement(['Entity', 'Individual']));
        self::setter($node, 'uid', mt_rand(10000, 900000));
        self::setter($node, 'title', Faker::randomElement(['Aerospace Engineer', 'Agricultural Engineer', 'Automotive Engineer', 'Biological Engineer', 'Biomedical Engineer']));
    }

    /**
     * set the attribute of the node only if exists.
     *
     * @param $node array
     * @param $property string
     * @param $value string|int
     */
    protected static function setter($node, $property, $value, $subProperty = false)
    {

        //if there is a sub-property and there is only one, replace it
        if ($subProperty && isset($node->$property->$subProperty) && count($node->$property->$subProperty) === 1) {
            $node->$property->$subProperty = $value;
        } elseif (property_exists($node, $property)) {
            $node->$property = $value;
        }
    }
}

OfacCreator::createOfacFixtures();
