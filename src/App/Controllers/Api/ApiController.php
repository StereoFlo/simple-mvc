<?php

namespace App\Controllers\Api;

use Core\Http\Request;
use Core\Http\JsonResponse;

/**
 * Class ApiController
 * @package Controllers
 */
class ApiController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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