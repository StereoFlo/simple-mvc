<?php

namespace Core;

/**
 * Class Input
 * @package Core
 */
class Input
{
    /**
     * @param string|null $key
     * @return string|null
     */
    public static function takeGet(string $key = null)
    {
        if ($key) {
            return $_GET[$key] ?? null;
        }
        return $_GET;
    }

    /**
     * @param string|null $key
     * @return string|null
     */
    public static function takePost(string $key = null)
    {
        if ($key) {
            return $_POST[$key] ?? null;
        }
        return $_POST;
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