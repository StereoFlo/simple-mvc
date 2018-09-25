<?php
include_once '../src/bootstrap.php';

$request = \Core\Request\Request::create();
$router  = \Core\Router\Router::create($request);
Application::create($request, $router);