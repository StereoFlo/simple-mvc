<?php

namespace Core\Router;

use Core\Request;
use Core\Request\Bag;
use Core\Router\Collection\Route;

/**
 * Class Router
 * @package Core\Router
 */
class Router
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Bag
     */
    protected $server;

    /**
     * @var RequestedData
     */
    protected $requestedData;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param Request $request
     *
     * @return Router
     * @throws \Exception
     */
    public static function create(Request $request): self
    {
        return new self($request);
    }

    /**
     * Router constructor.
     *
     * @param Request $request
     *
     * @throws \Exception
     */
    public function __construct(Request $request)
    {
        $this->server = $request->server();
        $this->collection = new Collection();
        $this->requestedData = new RequestedData($request);
        return $this->initCurrentRoute();
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return Router
     */
    private function initCurrentRoute(): self
    {
        foreach ($this->collection->getStack() as $route) {
            if (\preg_match($route->getPath(), $this->requestedData->getUri(), $matches)) {
                $this->route = $route;
                unset($matches[0]);
                $this->params = $matches;
                break;
            }
        }
        return $this;
    }
}