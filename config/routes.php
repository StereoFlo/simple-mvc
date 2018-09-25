<?php

/**
 * app routes
 */
return [
    '/^\/$/' => [
        'method' => 'get',
        'controller' => \App\Controllers\IndexController::class,
        'action' => 'index',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/test\/(.\d+)\/(.\d+)\/(.\d+)\/?$/i' => [
        'method' => 'get',
        'controller' => \App\Controllers\IndexController::class,
        'action' => 'test',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/api\/test$/i' => [
        'method' => 'get',
        'controller' => \App\Controllers\Api\ApiController::class,
        'action' => 'index',
        'mode'   => Application::MODE_API,
    ],
];