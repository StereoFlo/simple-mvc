<?php

namespace Core;

use App\Utils;

/**
 * Class Router
 * @package Core
 */
class Router
{
    /**
     * @var string
     */
    private $method = '';

    /**
     * @var string
     */
    private $route = '';

    /**
     * @var string
     */
    private $mode = 0;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var null answer from controller
     */
    private static $out;

    /**
     * @param string $method
     * @param string $route
     *
     * @return Router
     */
    public static function create(string $method, string $route)
    {
        return new self($method, $route);
    }

    /**
     * Router constructor.
     *
     * @param string $method
     * @param string $route
     *
     * @return Router
     */
    private function __construct(string $method, string $route)
    {
        $this->method = $method;
        $this->route = $route;
        $router = Config::getConfig('router');
        $main = Config::getConfig('main');
        $this->config = $router + $main;
        if (preg_match(Utils::getProperty($router, 'staticFileExtensions'), $route)) {
            return $this;
        }
        return $this->run();
    }

    /**
     * @return null
     */
    public static function getOut()
    {
        return self::$out;
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * @return mixed
     */
    private function run()
    {
        $routes = Config::getConfig('routes');
        if (empty($routes)) {
            Response::error503();
            return false;
        }
        list($route, $params) = $this->getRouteAndParams($routes);
        if (!$route) {
            Response::error404();
            return false;
        }
        $this->mode = Utils::getProperty($route, 'mode', '');
        $method = Utils::getProperty($route, 'method', '');
        if ($this->method !== strtoupper($method)) {
            Response::error400();
            return false;
        }
        $path = $this->getControllerPath(Utils::getProperty($route, 'controller'));
        if (!file_exists($path)) {
            Response::error503();
            return false;
        }
        $namespace = $this->getControllerNamespace(Utils::getProperty($route, 'controller'));
        if (!is_callable($namespace, Utils::getProperty($route, 'action'))) {
            Response::error503();
            return false;
        }
        if (empty($params)) {
            static::$out = $this->call([$namespace, Utils::getProperty($route, 'action')], $params);
            return true;
        }
        static::$out = $this->call([$namespace, Utils::getProperty($route, 'action')], $params);
        return true;
    }

    /**
     * @param array $callback
     * @param array $args
     *
     * @return mixed
     */
    private function call($callback = [], $args = [])
    {
        return call_user_func_array($callback, $args);
    }

    /**
     * @param string $what
     *
     * @return string
     */
    private function prepareForNs(string $what): string
    {
        return str_replace("/", "\\", $what);
    }

    /**
     * @param string $what
     *
     * @return string
     */
    private function prepareForPath(string $what): string
    {
        return str_replace('\\', '/', $what);
    }

    /**
     * @param $routes
     * @return array
     */
    private function getRouteAndParams($routes): array
    {
        $route = null;
        $params = [];
        foreach ($routes as $key => $value) {
            if (preg_match($key, $this->route, $matches)) {
                $route = $value;
                unset($matches[0]);
                $params = $matches;
                break;
            }
        }
        return [$route, $params];
    }

    /**
     * @param string $controller
     *
     * @return string
     */
    private function getControllerPath(string $controller): string
    {
        $path = Utils::getProperty($this->config, 'srcDir') . DS . $this->prepareForPath($controller) . Utils::getProperty($this->config, 'extension');
        return $path;
    }

    /**
     * @param string $controller
     *
     * @return string
     */
    private function getControllerNamespace(string $controller): string
    {
        $namespace = $this->prepareForNs($controller);
        return $namespace;
    }
}