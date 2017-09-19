<?php

namespace App\Boot;

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
        print json_encode($out);
        return true;
    }
}