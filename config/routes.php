<?php

/**
 * app routes
 */
return [
    '/^\/$/' => [
        'method' => 'get',
        'controller' => 'index',
        'action' => 'index',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/test\/(.\d+)\/(.\d+)\/(.\d+)(\/)?$/i' => [
        'method' => 'get',
        'controller' => 'index',
        'action' => 'test',
        'mode'   => Application::MODE_WEB,
    ],
    '/^\/api\/test$/i' => [
        'method' => 'get',
        'controller' => 'Api/Test',
        'action' => 'index',
        'mode'   => Application::MODE_API,
    ],

];