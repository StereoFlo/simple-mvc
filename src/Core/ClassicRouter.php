<?php

namespace Core;

use \ReflectionMethod;

/**
 * Class Router
 * @package Core
 */
class ClassicRouter
{
    private static $params = [];

    /**
     * @param string $path
     *
     * @return bool
     * @throws \Exception
     */
    public static function init(string $path)
    {
        $config = Config::getConfig('router');
        $mainConfig = Config::getConfig('main');

        $url = trim($path, '/');
        if (preg_match($config['staticFileExtensions'], $path)) {
            return false;
        }
        $data = explode('/', $url);
        $controller = $config['controllersNamespace'];
        $dirPart = $config['controllersDir'];
        foreach ($data as $key => $part) {
            if (empty($part)) {
                $controller .= '\\' . $config['defaultController'];
                $dirPart .= DS . $config['defaultController'] . $mainConfig['extension'];
                continue;
            }
            if (file_exists($dirPart) && is_dir($dirPart)) {
                $controller .= '\\' . $part;
                $dirPart .= DS . $part;
                if (is_file($dirPart . DS . $mainConfig['extension'])) {
                    $dirPart .= $dirPart . DS . $mainConfig['extension'];
                }
                unset($data[$key]);
                continue;
            }
            static::$params[] = $data[$key];
        }

        $method = self::getMethod($config);
        $params = static::$params;
        $res = self::callController($controller, $method, $params);
        if (!$res) {
           Response::error404();

            return false;
        }

        return $res;
    }

    /**
     * @param string $controller
     * @param string $method
     * @param        $data
     * @param        $args
     *
     * @return mixed
     */
    private static function call(string $controller, string $method, array $data, int $args = 0)
    {
        for ($i = 0; $i < $args; $i++) {
            unset($data[$i]);
        }
        $classMethod = new ReflectionMethod($controller, $method);
        $numberOfParams = $classMethod->getNumberOfParameters();
        $dataParams = count($data);
        if ($numberOfParams != $dataParams) {
            for ($i = 0; $i < ($numberOfParams - $dataParams); $i++) {
                $data[] = null;
            }
        }

        return call_user_func_array([$controller, $method], $data);
    }

    /**
     * @param string $controller
     * @param string $method
     * @param array  $params
     *
     * @return bool
     */
    private static function callController(string $controller, string $method, array $params)
    {
        if (is_callable([$controller, $method])) {
            return self::call($controller, $method, $params, count($params));
        }

        return false;
    }

    /**
     * @param $config
     *
     * @return mixed
     */
    private static function getMethod($config)
    {
        if (isset(static::$params[0]) && !empty(static::$params[0])) {
            $method = static::$params[0];
            unset(static::$params[0]);

            return $method;
        }

        return $config['defaultMethod'];
    }
}