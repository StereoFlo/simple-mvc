<?php

namespace Core;

use App\Utils;

/**
 * Class Input
 * @package Core
 */
class Request
{
    /**
     * @param string $key
     * @return string|null
     */
    public static function takeGet(string $key = '')
    {
        if (empty($key)) {
            return $_GET;
        }
        return Utils::getProperty($_GET, $key);
    }

    /**
     * @param string|null $key
     * @return string|null
     */
    public static function takePost(string $key = '')
    {
        if (empty($key)) {
            return $_POST;
        }
        return Utils::getProperty($_POST, $key);
    }

    /**
     * @return bool
     */
    public static function hasPost()
    {
        return !empty($_POST);
    }

    /**
     * @return bool
     */
    public static function hasGet()
    {
        return !empty($_GET);
    }
}