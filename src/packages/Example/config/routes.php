<?php

/**
 * example routes
 */
return [
    '/^\/example(\/)?$/' => [
        'method' => 'get',
        'controller' => \Example\Controllers\ExampleController::class,
        'action' => 'index',
        'mode'   => Application::MODE_WEB,
    ],
];