<?php
include_once '../src/bootstrap.php';

$container = new \Core\Container();
$request   = \Core\Request\Request::create();
$router    = \Core\Router\Router::create($request);
Application::create($request, $router, $container);