<?php


namespace Core\Database\QueryBuilder;

/**
 * Interface PartInterface
 * @package Calculator\Application\DataBase\QueryBuilder
 */
interface PartInterface
{
    /**
     * @return string
     */
    public function __toString(): string ;

    /**
     * @return string
     */
    public function getResult(): string ;

    /**
     * @return static
     */
    public function process();
}