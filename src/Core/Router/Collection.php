<?php

namespace Core\Router;

use App\Utils;
use Core\Config;
use Core\Router\Collection\Route;

/**
 * Class Collection
 * @package Core\Router
 */
class Collection
{
    const ROUTES_CONFIG_KEY = 'routes';

    /**
     * @var Route[]
     */
    protected $stack = [];

    /**
     * @var bool
     */
    protected $isEmpty = false;

    /**
     * Collection constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->initFromContig();
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->stack;
    }

    /**
     * @param Route $route
     *
     * @return Collection
     */
    public function addRoute(Route $route): self
    {
        $this->stack[] = $route;
        return $this;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    protected function initFromContig(): self
    {
        $routes = Config::getConfig(self::ROUTES_CONFIG_KEY);
        if (empty($routes)) {
            $this->isEmpty = true;
            return $this;
        }
        foreach ($routes as $path => $routeData) {
            $controller = Utils::getProperty($routeData, 'controller');
            $method     = Utils::getProperty($routeData, 'method');
            $action     = Utils::getProperty($routeData, 'action');
            $mode       = Utils::getProperty($routeData, 'mode');
            $this->addRoute(new Route($path, $controller, $method, $action, $mode));
        }
        return $this;
    }
}