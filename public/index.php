<?php
require_once __DIR__ . '/../src/Application.php';
define('DS', DIRECTORY_SEPARATOR);
Application::run(Application::MODE_WEB, $_SERVER['REQUEST_URI']);