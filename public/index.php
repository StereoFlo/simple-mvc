<?php

use Core\Application;
use Core\Container;
use Core\Http\Request;
use Core\Router\Router;

//include_once '../src/bootstrap.php';
require_once '../vendor/autoload.php';
require_once '../src/bootstrap.php';

$container = new Container();
$request   = Request::create();
$router    = Router::create($request);

Application::create($request, $router, $container);