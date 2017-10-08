<?php

namespace App\Boot;

/**
 * Class AbstractBoot
 */
abstract class AbstractBoot
{
    /**
     * @param $out
     *
     * @return mixed
     */
    abstract public static function run($out);
}