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
            if (!file_exists(self::getConfigPath())) {
                throw new \Exception('Config is not exists!');
            }
            self::mergeConfig($configName, require_once self::getConfigPath());
        }
        if (!$key) {
            return static::$config[$configName];
        }
        return self::getFromConfig($key);
    }

    /**
     * @param string|null $key
     *
     * @return array
     */
    private static function getFromConfig(string $key = null)
    {
        if (empty($key) && !isset(self::$config[self::$configName][$key])) {
            return [];
        }
        return self::$config[self::$configName][$key];
    }

    /**
     * @param string $name
     * @param array  $includedConfig
     *
     * @return array
     */
    private static function mergeConfig(string $name, array $includedConfig): array
    {
        if (isset(static::$config[$name])) {
            static::$config[$name] = static::$config[$name] + $includedConfig;
            return static::$config;
        }

        static::$config[$name] = $includedConfig;
        return static::$config;
    }

    /**
     * @return string
     */
    private static function getConfigPath(): string
    {
        return CONFIG_PATH . DS . static::$configName . \PHP_EXTENSION;
    }
}