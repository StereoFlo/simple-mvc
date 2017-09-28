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
     * @param $viewName
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    final public static function view(string $viewName, array $params = [])
    {
        $mainConfig = Config::getConfig('main');
        if (Utils::getProperty($mainConfig, 'isPackage') && Utils::getProperty($mainConfig, 'packageViewPath')) {
            $file = realpath(Utils::getProperty($mainConfig, 'packageViewPath') . $viewName . Utils::getProperty($mainConfig, 'viewExtension'));
        } else {
            $file = realpath(Utils::getProperty($mainConfig, 'viewPath') . $viewName . Utils::getProperty($mainConfig, 'viewExtension'));
        }
        if (!file_exists($file)) {
            throw new \Exception($file . ' template file is not exists');
        }
        foreach ($params as $var => $val) {
            $$var = $val;
        }
        return include $file;
    }
}