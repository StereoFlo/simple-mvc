<?php

namespace Controllers\Api;

use Core\ApiController;

/**
 * Class Index
 * @package Controllers
 */
class Test extends ApiController
{
    public static function index()
    {
        return self::respond(['test' => 'value']);
    }
}