<?php

namespace App\Boot;

use Core\Mime;
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
     * @return bool
     */
    public static function run($out)
    {
        print Response::create($out, Mime::JSON)->json();
        return true;
    }
}