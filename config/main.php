<?php

return [

    'viewExtension' => '.php',
    'viewPath' => '../src' . DS . 'Views' . DS,
    'packagesPath' => '../src' . DS . 'Packages',

    /**
     *  Logger config
     */
    'logger' => [
        'path' => '../tmp/log',
        'prefix' => 'appLog' . date('Y-m-d')
    ],

    /**
     * Database config
     */
    'database' => [
        'host' => 'localhost',
        'user' => 'dir',
        'password' => 'dir',
        'basename' => 'dir'
    ],
];