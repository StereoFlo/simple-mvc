<?php

/**
 * app routes
 */

use Core\Router\Collection\Route;

return [
    new Route('/^\/$/', \App\Controllers\IndexController::class, 'get', 'index'),
    new Route('/^\/test\/(.\d+)\/(.\d+)\/(.\d+)\/?$/i', \App\Controllers\IndexController::class, 'get', 'test'),
    new Route('/^\/api\/test\/(.\d+)$/i', \App\Controllers\Api\ApiController::class, 'get', 'test'),
    new Route('/^\/api\/index$/i', \App\Controllers\Api\ApiController::class, 'get', 'index'),
];