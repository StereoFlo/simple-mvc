<?php


namespace Core\Model\QueryBuilder;

/**
 * Class AbstractPart
 * @package Calculator\Application\DataBase\QueryBuilder
 */
abstract class AbstractPart implements PartInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $result;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getResult();
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        $this->process();
        return $this->result;
    }

    /**
     * @return static
     */
    abstract public function process();
}