<?php
/**
 * Created by PhpStorm.
 * User: HOME-PC01
 * Date: 16.10.2016
 * Time: 18:02
 */

namespace Core;

/**
 * Class Controller
 * @package Core
 */
abstract class Controller
{
    /**
     * @param $page
     * @param array $arr
     * @return bool
     * @throws \Exception
     */
    final public static function view($page, $arr = [])
    {
        $mainConfig = Config::getConfig('main');
        $file = realpath($mainConfig['viewPath'] . $page . $mainConfig['viewExtension']);
        if (file_exists($file)) {
            foreach ($arr as $var => $val) {
                $$var = $val;
            }
            include $file;
            return self::class;
        }
        throw new \Exception($file . ' template file is not exists');
    }
}