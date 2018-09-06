<?php

namespace App\Boot;

use Core\Response;

/**
 * Class Api
 * @package App\Boot
 */
class Api extends AbstractBoot
{
    /**
     * @param $out
     *
     * @return string
     */
    public static function run($out)
    {
        print Response::json($out);
        return true;
    }
}