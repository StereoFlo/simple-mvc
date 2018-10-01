<?php

namespace Core\Model\Connection;

/**
 * Class Connection
 * @package Core\Model
 */
class MysqliConnection extends AbstractConnection
{
    /**
     * @return MysqliConnection
     * @throws \Exception
     */
    public function initConnection(): self
    {
        static::$connection = new MysqliConnection(static::$host, static::$user, static::$password, static::$dbName, static::$port, static::$prefix);
        if (static::$connection->connect_errno) {
            throw new \Exception(("Failed to connect to MySQL: (" . static::$connection->connect_errno . ") " . static::$connection->connect_error));
        }
        return $this;
    }

    /**
     * @return \Mysqli
     */
    public function getConnection()
    {
        return static::$connection;
    }
}