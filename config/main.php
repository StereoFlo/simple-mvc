<?php

return [
    'viewPath' => APP_DIR . DS . 'Views',

    /**
     *  Logger config
     */
    'logger'   => [
        'path'   => '../tmp/log',
        'prefix' => 'appLog' . date('Y-m-d'),
    ],

    /**
     * Database config
     */
    'database' => [
        'type'     => 'mysqli',
        'host'     => 'localhost',
        'user'     => 'simple',
        'password' => 'simple',
        'dbname'   => 'simple',
    ],
];