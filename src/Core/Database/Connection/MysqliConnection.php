<?php

namespace Core\Database\Connection;

/**
 * Class Connection
 * @package Core\Database
 */
class MysqliConnection extends AbstractConnection
{
    /**
     * @return MysqliConnection
     * @throws \Exception
     */
    public function initConnection(): self
    {
        static::$connection = new \mysqli(static::$host, static::$user, static::$password, static::$dbName, static::$port, static::$prefix);
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

    /**
     * @param string $sql
     *
     * @return array
     */
    public function fetchAssocArray(string $sql)
    {
        return static::$connection->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchRow(string $sql)
    {
        return static::$connection->query($sql)->fetch_row();
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchArray(string $sql)
    {
        return static::$connection->query($sql)->fetch_array();
    }
}