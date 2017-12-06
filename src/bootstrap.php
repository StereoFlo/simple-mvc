<?php
define('DS', DIRECTORY_SEPARATOR);
define('SRC_DIR', realpath('..' . DS . 'src'));
define('CONFIG_PATH', realpath('..' . DS . 'config'));
define('PHP_EXTENSION', '.php');
require_once __DIR__ . '/../src/Application.php';