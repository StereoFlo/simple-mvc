<?php

use Core\Container;
use Core\Request\Request;
use Core\Router\Router;

include_once '../src/bootstrap.php';

$container = new Container();
$request   = Request::create();
$router    = Router::create($request);

Application::create($request, $router, $container);