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
        if (\preg_match(Utils::getProperty($router, 'staticFileExtensions'), $route)) {
            return $this;
        }
        return $this->run();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function run()
    {
        $routes = Config::getConfig('routes');
        if (empty($routes)) {
            throw new \Exception('Routes config cannot be loaded');
        }
        list($route, $params) = $this->getRouteAndParams($routes);
        if (!$route) {
            throw new \Exception('route for ' . printf($routes, true) . ' not found');
        }
        $this->mode = Utils::getProperty($route, 'mode', '');
        $method = Utils::getProperty($route, 'method', '');
        if (!empty($this->method) && $this->method !== strtoupper($method)) {
            throw new \Exception('Bad method for this controller');
        }
        $path = $this->getControllerPath(Utils::getProperty($route, 'controller'));
        if (!file_exists($path)) {
            throw new \Exception('Controller file not found');
        }
        $namespace = $this->getControllerNamespace(Utils::getProperty($route, 'controller'));
        if (!\is_callable($namespace, Utils::getProperty($route, 'action'))) {
            throw new \Exception('Controller or namespace is not callable');
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
        return \call_user_func_array($callback, $args);
    }

    /**
     * @param string $what
     *
     * @return string
     */
    private function prepareForNs(string $what): string
    {
        return \str_replace("/", "\\", $what);
    }

    /**
     * @param string $what
     *
     * @return string
     */
    private function prepareForPath(string $what): string
    {
        return \str_replace('\\', '/', $what);
    }

    /**
     * @param $routes
     *
     * @return array
     */
    private function getRouteAndParams($routes): array
    {
        $route = null;
        $params = [];
        foreach ($routes as $key => $value) {
                if (\preg_match($key, $this->route, $matches)) {
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
     * @throws \Exception
     */
    private function getControllerPath(string $controller): string
    {
        $path = Utils::getProperty($this->config, 'srcDirPath') . DS . $this->prepareForPath($controller) . \Application::PHP_EXTENSION;
        if (!file_exists($path)) {
            $path = Utils::getProperty($this->config, 'packagesPath') . DS . $this->prepareForPath($controller) . \Application::PHP_EXTENSION;
            if (!file_exists($path)) {
                throw new \Exception('Controller path not found');
            }
        }
        include_once $path;
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