<?php

namespace App\Boot;

use Core\Mime;
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
        Response::applyContentType(Mime::HTML, 'utf-8');
        Response::applyNoCache(true);
        return $out;
    }
}