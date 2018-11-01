<?php

namespace Core;

use App\Utils;

/**
 * Class Controller
 * @package Core
 */
class Template
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
    public function render(string $viewName, array $params = [])
    {
        $mainConfig = Config::getConfig('main');
        $filePath = \realpath(Utils::getProperty($mainConfig, 'viewPath') . DS . $viewName . \PHP_EXTENSION);
        if (!\file_exists($filePath)) {
            throw new \Exception($filePath . ' template file is not exists');
        }
        foreach ($params as $var => $val) {
            $$var = $val;
        }
        if (empty(self::$loadedViews[\md5($filePath)])) {
            self::$loadedViews[\md5($filePath)] = require_once $filePath;
            return self::$loadedViews[\md5($filePath)];
        }
        return self::$loadedViews[\md5($filePath)];
    }
}