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
    public static function run($out): bool
    {
        Response::applyContentType(Mime::JSON, 'utf-8');
        Response::applyNoCache(true);
        print \json_encode($out);
        return true;
    }
}