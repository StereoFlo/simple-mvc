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
    public function getStack(): array
    {
        return $this->stack;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    protected function initFromContig(): self
    {
        $routes = Config::getConfig('routes');
        if (empty($routes)) {
            $this->isEmpty = true;
            return $this;
        }
        foreach ($routes as $path => $routeData) {
            $this->stack[] = new Route($path, $routeData['method'], $routeData['action'], $routeData['mode']);
        }
        return $this;
    }
}