<?php
defined('ABSPATH') or die('No Way!');

/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 02/09/18
 * Time: 14:08
 */
class Param
{
    /**
     * @param $param
     * @return string
     * MOVE
     */
    public static function format_optional($param)
    {
        return '(?:/(?P<' . $param . '>[^(^\r\n\t\f\v)/]+))?';
    }


    /**
     * @param $param
     * @return string
     * MOVE
     */
    public static function format_required($param)
    {
        return '/(?P<' . $param . '>[^(^\r\n\t\f\v)/]+)';
    }


    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     * MOVE
     */
    public static function ends_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }
        return false;
    }


    /**
     * Check if uri has a parameter
     *
     * @param $last_part
     * @return int
     * MOVE
     */
    public static function is_param($last_part)
    {
        return preg_match('/\{.*?\}/', $last_part);
    }
}