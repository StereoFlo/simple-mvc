<?php

namespace Controllers;

use Core\Controller;
use Core\Session;

/**
 * Class Index
 * @package Controllers
 */
class Index extends Controller
{

    public static function index()
    {
        Session::start();
        return self::view('index');
    }

    public static function test(string $test1, string $test2, string $test3)
    {
        var_dump(func_get_args());
    }
}