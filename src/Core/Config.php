<?php

namespace Core;
/**
 * Class Config
 */
class Config
{
    private static $configPath = '../config';
    private static $phpExtension = 'php';

    private static $config = [];
    private static $configName = '';

    /**
     * @param string $configName
     * @param string $key
     * @return array
     */
    public static function getConfig(string $configName, string $key = null)
    {
        self::$configName = $configName;
        $fullPath = static::$configPath . DIRECTORY_SEPARATOR . $configName . '.' . static::$phpExtension;
        if (file_exists($fullPath)) {
            if (isset(static::$config[self::$configName])) {
                return self::getFromConfig($key);
            }
            self::$configName = $configName;
            self::$config[$configName] = require_once $fullPath;
            return self::getFromConfig($key);
        }
        return [];
    }

    private static function getFromConfig(string $key = null)
    {
        if ($key && isset(self::$config[self::$configName][$key])) {
            return self::$config[self::$configName][$key];
        }
        return self::$config[self::$configName];
    }
}