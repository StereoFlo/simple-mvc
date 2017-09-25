<?php

namespace Example\Controllers;

use Core\Controller;

/**
 * Class ExampleController
 * @package Example
 */
class ExampleController extends Controller
{

    /**
     * @return int
     */
    public static function index()
    {
       return self::view('index');
    }
}