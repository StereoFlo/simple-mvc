<?php

return [
    'viewPath'     => APP_DIR . DS . 'Views',

    /**
     *  Logger config
     */
    'logger'       => [
        'path'   => '../tmp/log',
        'prefix' => 'appLog' . date('Y-m-d'),
    ],

    /**
     * Database config
     */
    'database'     => [
        'host'     => 'localhost',
        'user'     => 'dir',
        'password' => 'dir',
        'basename' => 'dir',
    ],
];