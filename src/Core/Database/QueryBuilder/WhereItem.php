<?php


namespace Core\Database\QueryBuilder;

/**
 * Class WhereItem
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class WhereItem
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $expr;

    /**
     * WhereItem constructor.
     *
     * @param string $key
     * @param string $value
     * @param string $operator
     * @param string $expr
     */
    public function __construct(string $key, string $value, string $operator = Where::OPERATOR_AND, string $expr = '=')
    {
        $this->key      = $key;
        $this->value    = $value;
        $this->operator = $operator;
        $this->expr     = $expr;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getExpr(): string
    {
        return $this->expr;
    }
}