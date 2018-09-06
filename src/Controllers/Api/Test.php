<?php

namespace Controllers\Api;

use Core\Controller;

/**
 * Class Index
 * @package Controllers
 */
class Test extends Controller
{
    public static function index()
    {
        return ['test'];
    }
}