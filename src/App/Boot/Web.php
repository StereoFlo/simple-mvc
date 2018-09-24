<?php

namespace App\Boot;

use Core\Response;

/**
 * Class Web
 * @package App\Boot
 */
class Web extends AbstractBoot
{
    /**
     * @param $out
     *
     * @return mixed
     */
    public static function run($out)
    {
        return Response::create($out)->html();
    }
}