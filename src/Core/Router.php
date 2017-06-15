<?php
/**
 * File:    Router.php
 * Project: simple-mvc
 * User:    evgen
 * Date:    15.06.17
 * Time:    10:12
 */

namespace Core;


class Router
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $router;

    /**
     * @return Router
     */
    public static function init()
    {
        return new self();
    }

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->routes = Config::getConfig('routes');
        $this->config = Config::getConfig('main');
        $this->router = Config::getConfig('router');
        return $this->run();
    }

    /**
     * @return string
     */
    public function getUri()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI']);
        }

        if (!empty($_SERVER['PATH_INFO'])) {
            return trim($_SERVER['PATH_INFO'], '/');
        }

        if (!empty($_SERVER['QUERY_STRING'])) {
            return trim($_SERVER['QUERY_STRING'], '/');
        }

        return '';
    }

    /**
     *
     */
    public function run()
    {
        $uri = $this->getUri();
        foreach ($this->routes as $pattern => $route) {
            if (preg_match("/$pattern/", $uri)) {
                $internalRoute  = preg_replace("/$pattern/", $route, $uri);
                $segments       = explode('/', $internalRoute);
                $controller     = ucfirst(array_shift($segments));
                $action         = array_shift($segments);
                $parameters     = $segments;
                $controllerFile = $this->router['controllersDir'] . DS . $controller . $this->config['extension'];
                if (!file_exists($controllerFile)) {
                    Response::error404();
                    return false;
                }
                include($controllerFile);

                $controller = $this->router['controllersNamespace']. '\\' . $controller;
                if (!is_callable([$controller, $action])) {
                    Response::error404();
                    return false;
                }
                return call_user_func_array([$controller, $action], $parameters);
            }
        }
        Response::error404();
        return false;
    }
}