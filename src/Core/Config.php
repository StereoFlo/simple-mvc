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
    private static $configPath = 'config';

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
            $fullPath = '..' . DS . static::$configPath . DIRECTORY_SEPARATOR . static::$configName . \Application::PHP_EXTENSION;
            if (!file_exists($fullPath)) {
                if (!self::getPackageConfig($configName)) {
                    return [];
                }
            }
            self::getPackageConfig($configName);
            self::mergeConfig($configName, require_once $fullPath);
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

    /**
     * @param $configName
     *
     * @return bool
     */
    private static function getPackageConfig($configName): bool
    {
        $realPath = realpath(__DIR__ . DS . '..' . DS . 'Packages');
        $dirContent = scandir($realPath);
        if (empty($dirContent)) {
            return false;
        }
        foreach ($dirContent as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }
            $fullPath = $realPath . DS . $dir;
            if (!is_dir($fullPath)) {
                continue;
            }
            $configFile = $fullPath . DS . static::$configPath . DS . static::$configName . \Application::PHP_EXTENSION;
            if (!file_exists($configFile)) {
                continue;
            }
            $getArray = include_once $configFile;
            self::mergeConfig($configName, $getArray + ['isPackage' => true]);
        }
        return false;
    }

    /**
     * @param string $name
     * @param array  $includedConfig
     *
     * @return bool
     */
    private static function mergeConfig(string $name, array $includedConfig): bool
    {
        if (isset(static::$config[$name])) {
            static::$config[$name] = static::$config[$name] + $includedConfig;
        } else {
            static::$config[$name] = $includedConfig;
        }
        return true;
    }
}