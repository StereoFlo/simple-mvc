<?php
/**
 * Created by PhpStorm.
 * User: HOME-PC01
 * Date: 16.10.2016
 * Time: 18:02
 */

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
    final public static function view($viewName, $params = [])
    {
        $mainConfig = Config::getConfig('main');
        $file = realpath(Utils::getProperty($mainConfig, 'viewPath') . $viewName . Utils::getProperty($mainConfig, 'viewExtension'));
        if (!file_exists($file) && Config::isPackage()) {
            $file = realpath('../src/packages' . DS . Config::getPackageName() . DS . $viewName . $mainConfig['viewExtension']);
            if (!file_exists($file)) {
                throw new \Exception($file . ' template file is not exists');
            }
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