<?php

namespace App\Controllers;

use Core\Controller;
use Core\Response\Response;

/**
 * Class Index
 * @package Controllers
 */
class Index extends Controller
{
    /**
     * @throws \Exception
     */
    public static function index()
    {
        return Response::create('privet');
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