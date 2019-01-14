<?php

namespace Core\Router;

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
        foreach ($routes as $routeData) {
            $this->addRoute($routeData);
        }
        return $this;
    }
}