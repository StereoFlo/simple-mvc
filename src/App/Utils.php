<?php

namespace App;

/**
 * Class Utils
 * @package Core
 */
class Utils
{
    /**
     * @param mixed $data
     * @param       $key
     * @param null $default
     *
     * @return mixed|null
     */
    public static function getProperty($data, $key, $default = null)
    {
        if (\is_array($data) && isset($data[$key])) {
            return $data[$key];
        }
        if (\is_object($data) && isset($data->{$key})) {
            return $data->{$key};
        }
        return $default;
    }
}