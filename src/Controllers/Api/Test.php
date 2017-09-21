<?php

namespace Controllers\Api;

use Core\Controller;
use Models\TestModel;

/**
 * Class Index
 * @package Controllers
 */
class Test extends Controller
{
    public static function index()
    {
        return TestModel::create()->getMedia();
    }
}