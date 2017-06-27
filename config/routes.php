<?php

/**
 * app routes
 */
return [
    '/^\/$/' => [
        'method' => 'get',
        'controller' => 'index',
        'action' => 'index',
        'params' => null,
    ],
    '/^\/test\/(.*)\/(.*)\/(.*)$/i' => [
        'method' => 'get',
        'controller' => 'index',
        'action' => 'test',
        'params' => [
            '$1',
            '$2',
            '$3',
        ],
    ]

];