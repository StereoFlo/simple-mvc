<?php

namespace Core\Database\Connection;

/**
 * Class PdoConnection
 * @package Core\Database\Connection
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

    /**
     * @param string $sql
     *
     * @return array
     */
    public function fetchAssocArray(string $sql)
    {
        return static::$connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchRow(string $sql)
    {
        return static::$connection->query($sql)->fetchColumn();
    }

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchArray(string $sql)
    {
        return static::$connection->query($sql)->fetchAll();
    }
}