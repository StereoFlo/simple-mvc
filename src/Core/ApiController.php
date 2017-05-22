<?php
/**
 * Created by PhpStorm.
 * User: HOME-PC01
 * Date: 16.10.2016
 * Time: 18:02
 */

namespace Core;

/**
 * Class Controller
 * @package Core
 */
abstract class ApiController
{
    const FORMAT_JSON = 'json';

    /**
     * @param array  $data
     * @param string $format
     *
     * @return mixed
     */
    final public static function respond(array $data, $format = self::FORMAT_JSON)
    {
        if (!is_array($data)) {
            throw new \RuntimeException('Data is not an array');
        }
        switch ($format) {
            case self::FORMAT_JSON:
            default:
                print json_encode($data, JSON_PRETTY_PRINT);
                return;
                break;
        }
    }
}