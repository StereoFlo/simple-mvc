<?php

return [
    'viewPath'     => SRC_DIR . DS . 'Views',
    'packagesPath' => SRC_DIR . DS . 'Packages',

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