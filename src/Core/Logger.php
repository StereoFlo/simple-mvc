<?php

namespace Core;

use App\Utils;

/**
 * Class Logger
 * @package src\Core
 */
class Logger
{
    /**
     * @param string $message
     *
     * @return bool
     * @throws \Exception
     */
    public static function logToFile(string $message = ''): bool
    {
        $logs = Config::getConfig('main', 'logger');
        return \file_put_contents(Utils::getProperty($logs, 'path') . DIRECTORY_SEPARATOR . Utils::getProperty($logs, 'prefix') . PHP_EXTENSION, $message . PHP_EOL, FILE_APPEND) > 0;
    }
}