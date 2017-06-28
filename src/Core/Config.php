<?php

namespace Core;

/**
 * Class Config
 */
class Config
{
    /**
     * @var string
     */
    private static $configPath = '../config';

    /**
     * @var array
     */
    private static $config = [];

    /**
     * @var string
     */
    private static $configName = '';

    /**
     * @param string $configName
     * @param string $key
     * @return mixed
     */
    public static function getConfig(string $configName, string $key = null)
    {
        if (!isset(static::$config[$configName])) {
            static::$configName = $configName;
            $fullPath = static::$configPath . DIRECTORY_SEPARATOR . static::$configName . \Application::PHP_EXTENSION;
            if (!file_exists($fullPath)) {
                return [];
            }
            static::$config[$configName] = require_once $fullPath;
        }
        if (!$key) {
            return static::$config[$configName];
        }
        return self::getFromConfig($key);
    }

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    private static function getFromConfig(string $key = null)
    {
        if (!$key && !isset(self::$config[self::$configName][$key])) {
            return false;
        }
        return self::$config[self::$configName][$key];
    }
}