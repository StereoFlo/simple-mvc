<?php

use Core\Request\Request;
use Core\Router\Router;

include_once '../src/bootstrap.php';
include_once '../config/container.php';

$request = Request::create();
$router  = Router::create($request);

Application::create($request, $router, $container);