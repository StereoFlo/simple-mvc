<?php

namespace Core;

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
        if (preg_match($router['staticFileExtensions'], $route)) {
            return $this;
        }
        $this->run();
        return $this;
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
        if ($routes) {

        }
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
        $path = $this->config['controllersDir'] . DS . $route['controller'] . $this->config['extension'];
        if (!file_exists($path)) {
            Response::error503();
            return false;
        }
        $namespace = $this->config['controllersNamespace'] . '\\' . $this->prepareForNs($route['controller']);
        if (!is_callable($namespace, $route['action'])) {
            Response::error503();
            return false;
        }
        if (!empty($params)) {
            return call_user_func_array([$namespace, $route['action']], $params);
        }
        return call_user_func([$namespace, $route['action']]);
    }
    /**
     * @param string $controller
     *
     * @return string
     */
    private function prepareForNs(string $controller): string
    {
        return str_replace("/", "\\", $controller);
    }
}