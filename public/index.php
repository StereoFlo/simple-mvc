<?php
require_once __DIR__ . '/../src/Autoloader.php';
define('DS', DIRECTORY_SEPARATOR);
Autoloader::run();
Application::run(Application::MODE_WEB, $_SERVER['REQUEST_URI']);