<?php

namespace Core\Database;

use Core\Config;
use Core\Database\Connection\AbstractConnection;
use Core\Database\Connection\MysqliConnection;
use Core\Database\Connection\PdoConnection;

class DB
{
    /**
     * @return MysqliConnection|PdoConnection
     * @throws \Exception
     */
    public function getConnection()
    {
        $config = Config::getConfig('main', 'database');
        switch ($config['type']) {
            case AbstractConnection::MYSQLI:
                return new MysqliConnection($config['host'], $config['user'], $config['password'], $config['dbname']);
            case AbstractConnection::PDO:
                return new PdoConnection($config['host'], $config['user'], $config['password'], $config['dbname']);
            default:
                throw new \Exception('config does not contain a type of database');
        }
    }

    /**
     * @param null|string $table
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function getQueryBuilder(?string $table = null): QueryBuilder
    {
        return new QueryBuilder($table);
    }
}