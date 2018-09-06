<?php

namespace Controllers;

use Core\Controller;
use Core\Request;

/**
 * Class Index
 * @package Controllers
 */
class Index extends Controller
{
    /**
     * @return bool
     * @throws \Exception
     */
    public static function index()
    {
        $get = Request::create()->get();
        return self::view('index', $get);
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