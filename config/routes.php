<?php

/**
 * app routes
 */
return [
    '/^\/$/'                                 => [
        'method'     => 'get',
        'controller' => \App\Controllers\IndexController::class,
        'action'     => 'index',
    ],
    '/^\/test\/(.\d+)\/(.\d+)\/(.\d+)\/?$/i' => [
        'method'     => 'get',
        'controller' => \App\Controllers\IndexController::class,
        'action'     => 'test',
    ],
    '/^\/api\/test\/(.\d+)$/i'               => [
        'method'     => 'get',
        'controller' => \App\Controllers\Api\ApiController::class,
        'action'     => 'test',
    ],
    '/^\/api\/index$/i'                      => [
        'method'     => 'get',
        'controller' => \App\Controllers\Api\ApiController::class,
        'action'     => 'index',
    ],
];