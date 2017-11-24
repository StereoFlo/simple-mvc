<?php

namespace Core;

/**
 * Class Config
 */
class Config
{
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
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getConfig(string $configName, string $key = null)
    {
        if (!isset(static::$config[$configName])) {
            static::$configName = $configName;
            $fullPath = CONFIG_PATH . DS . static::$configName . \PHP_EXTENSION;
            if (!file_exists($fullPath)) {
                throw new \Exception('Config is not exists!');
            }
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