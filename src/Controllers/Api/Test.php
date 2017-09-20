<?php

namespace Controllers\Api;

use Core\ApiController;
use Models\TestModel;

/**
 * Class Index
 * @package Controllers
 */
class Test extends ApiController
{
    public static function index()
    {
        return TestModel::create()->getMedia();
    }
}