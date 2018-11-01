<?php

namespace Core\Database\Connection;

/**
 * Interface ConnectionInterface
 * @package Core\Database
 */
interface ConnectionInterface
{
    /**
     * @return static
     */
    public function initConnection();

    /**
     * @return \PDO|\Mysqli
     */
    public function getConnection();

    /**
     * @param string $sql
     *
     * @return array
     */
    public function fetchAssocArray(string $sql);

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchRow(string $sql);

    /**
     * @param string $sql
     *
     * @return mixed
     */
    public function fetchArray(string $sql);
}