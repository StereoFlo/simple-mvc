<?php

namespace Core\Model\Connection;

/**
 * Class PdoConnection
 * @package Core\Model\Connection
 */
class PdoConnection extends AbstractConnection
{

    /**
     * @return static
     */
    public function initConnection(): self
    {
        static::$connection = new \PDO('mysql:host='. static::$host .';dbname=' . static::$dbName, static::$user, static::$password);
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return self::$connection;
    }
}