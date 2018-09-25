<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request\Request;
use Core\Response\Response;

/**
 * Class IndexController
 * @package Controllers
 */
class IndexController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public static function index(Request $request): Response
    {
        $test = $request->query()->get('test', 'null');
        return Response::create(self::view('index', ['test' => $test]));
    }

    /**
     * @param string $test1
     * @param string $test2
     * @param string $test3
     */
    public static function test(string $test1, string $test2, string $test3)
    {
        var_dump(func_get_args());
    }
}