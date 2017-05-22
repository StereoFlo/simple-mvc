<?php
/**
 * Created by PhpStorm.
 * User: HOME-PC01
 * Date: 16.10.2016
 * Time: 17:06
 */

namespace Core;

/**
 * Class Mysql
 * @package Core
 */
class Model extends \mysqli
{
    /**
     * Mysql constructor.
     */
    public function __construct()
    {
        $config = Config::getConfig('mysql');
        parent::__construct($config['host'], $config['user'], $config['password'], $config['basename']);
    }
}