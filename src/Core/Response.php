<?php
/**
 * Created by PhpStorm.
 * User: evgen
 * Date: 29.05.17
 * Time: 20:39
 */

namespace Core;

/**
 * Class Response
 * @package Core
 */
class Response
{
    /**
     * 404 error
     */
    public static function error404()
    {
        header("HTTP/1.0 404 Not Found");
    }

    /**
     * 503 error
     */
    public static function error503()
    {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 300');
    }

    /**
     * 503 error
     */
    public static function error400()
    {
        header('HTTP/1.1 400 BAD REQUEST');
    }

}