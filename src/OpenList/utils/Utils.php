<?php

namespace ADiaz\AML\OpenList\utils;

/**
 * This file is part of the OpenList Parser utility.
 *
 * @category  PHP
 *
 * @author    Alberto Diaz <alberto@tytem.com>
 * @copyright 2016 Alberto Diaz <alberto@tytem.com>
 * @license   This source file is subject to the MIT license that is bundled
 *
 * @version Release: @package_version@
 *
 * @link http://tytem.com
 */
class Utils
{
    /**
     * GZIPs a file on disk (appending .gz to the name).
     *
     * Based on function by Simon East at:  http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
     *
     * @param string $source  Path to file that should be compressed
     * @param string $newFile new filename
     * @param int    $level   GZIP compression level (default: 9)
     *
     * @return string New filename (with .gz appended) if success, or false if operation fails
     */
    public static function gzCompressFile($source, $newFile, $level = 9)
    {
        $dest = $newFile.'.gz';
        $mode = 'wb'.$level;
        $error = false;
        if ($fp_out = gzopen($dest, $mode)) {
            if ($fp_in = fopen($source, 'rb')) {
                while (!feof($fp_in)) {
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                }
                fclose($fp_in);
            } else {
                $error = true;
            }
            gzclose($fp_out);
        } else {
            $error = true;
        }
        if ($error) {
            return false;
        } else {
            return $dest;
        }
    }

    /**
     * Utility function to remove empty values and null from json strings.
     *
     * @param string $json in json
     *
     * @return mixed cleaned
     */
    public static function removeEmptyAndNullJson($json)
    {
        return preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', preg_replace('/\[\]/', 'null', $json));
    }
}
