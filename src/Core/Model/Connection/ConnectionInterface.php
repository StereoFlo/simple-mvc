<?php

namespace Core\Model\Connection;

/**
 * Interface ConnectionInterface
 * @package Core\Model
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
}