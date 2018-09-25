<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Response\JsonResponse;

/**
 * Class ApiController
 * @package Controllers
 */
class ApiController extends Controller
{
    /**
     * @return JsonResponse
     */
    public static function index(): JsonResponse
    {
        return JsonResponse::create(['test' => 'json']);
    }
}