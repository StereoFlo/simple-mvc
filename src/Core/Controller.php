<?php

namespace Core;

use App\Utils;

/**
 * Class Controller
 * @package Core
 */
abstract class Controller
{
    /**
     * Hash for loaded views
     * @var array
     */
    protected static $loadedViews = [];

    /**
     * @param $viewName
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    final public static function view(string $viewName, array $params = [])
    {
        $mainConfig = Config::getConfig('main');
        $filePath = \realpath(Utils::getProperty($mainConfig, 'viewPath') . $viewName . Utils::getProperty($mainConfig, 'viewExtension'));
        if (!\file_exists($filePath)) {
            throw new \Exception($filePath . ' template file is not exists');
        }
        foreach ($params as $var => $val) {
            $$var = $val;
        }
        if (empty(self::$loadedViews[\md5($filePath)])) {
            self::$loadedViews[\md5($filePath)] = include_once $filePath;
            return self::$loadedViews[\md5($filePath)];
        }
        return self::$loadedViews[\md5($filePath)];
    }
}