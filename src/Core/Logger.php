<?php

namespace Core;

/**
 * Class Logger
 * @package src\Core
 */
class Logger
{
    /**
     * @param string $message
     * @return bool
     */
    public static function logToFile(string $message = ''): bool
    {
        $logs = Config::getConfig('common', 'logs');
        return file_put_contents($logs['path'] . DIRECTORY_SEPARATOR . $logs['prefix'] . '.php', $message . PHP_EOL, FILE_APPEND) > 0;
    }
}