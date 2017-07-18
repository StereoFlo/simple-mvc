<?php

/**
 * app routes
 */
return [
    '/^\/$/' => [
        'method' => 'get',
        'controller' => \Controllers\Index::class,
        'action' => 'index',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/test\/(.\d+)\/(.\d+)\/(.\d+)\/?$/i' => [
        'method' => 'get',
        'controller' => \Controllers\Index::class,
        'action' => 'test',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/api\/test$/i' => [
        'method' => 'get',
        'controller' => \Controllers\Api\Test::class,
        'action' => 'index',
        'mode'   => Application::MODE_API,
    ],

];