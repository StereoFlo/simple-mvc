<?php

use Core\ApiController;
use Core\ClassicRouter;
use Core\Logger;

/**
 * Class Application
 */
class Application
{
    /**
     * Modes
     */
    const MODE_API = 1,
          MODE_WEB = 2;

    /**
     * @param int   $mode
     * @param mixed $params
     *
     * @return mixed
     */
    public static function run(int $mode, $params)
    {
        try {
            switch ($mode) {
                case self::MODE_API:
                    return ApiController::respond(self::getRouter($params));
                    break;
                case self::MODE_WEB:
                default:
                    return self::getRouter($params);
                    break;
            }
        } catch (Exception $e) {
            Logger::logToFile($e->getCode() . ': ' . $e->getMessage());
            self::error503();
            return false;
        } catch (Throwable $t) {
            Logger::logToFile($t->getCode() . ': ' . $t->getMessage());
            self::error503();
            return false;
        }
    }

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
     * @param $params
     *
     * @return mixed
     */
    private static function getRouter($params)
    {
        return ClassicRouter::init($params);
    }
}