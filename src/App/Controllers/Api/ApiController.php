<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request\Request;
use Core\Response\JsonResponse;

/**
 * Class ApiController
 * @package Controllers
 */
class ApiController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public static function index(Request $request): JsonResponse
    {
        return JsonResponse::create(['server' => $request->server()->all()]);
    }

    /**
     * @param Request $request
     * @param string  $test
     *
     * @return JsonResponse
     */
    public function test(Request $request, string $test): JsonResponse
    {
        return JsonResponse::create([
            'server' => $request->server()->all(),
            'test'   => $test,
        ]);
    }
}