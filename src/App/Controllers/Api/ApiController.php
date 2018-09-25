<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Response\Response;

/**
 * Class ApiController
 * @package Controllers
 */
class ApiController extends Controller
{
    public static function index(): Response
    {
        return Response::create(['test' => 'json']);
    }
}