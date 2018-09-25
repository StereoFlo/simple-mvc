<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Response\Response;

/**
 * Class Index
 * @package Controllers
 */
class Test extends Controller
{
    public static function index()
    {
        return Response::create(['test' => 'json']);
    }
}