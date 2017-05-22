<?php
require_once __DIR__ . '/../src/Autoloader.php';
define('DS', DIRECTORY_SEPARATOR);
\spl_autoload_register('Autoloader::autoload');
Application::run(Application::MODE_WEB, $_SERVER['PATH_INFO']);