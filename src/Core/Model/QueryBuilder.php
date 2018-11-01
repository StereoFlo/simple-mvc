<?php

namespace Core\Model;


use Core\Model\QueryBuilder\GroupBy;
use Core\Model\QueryBuilder\Limit;
use Core\Model\QueryBuilder\OrderBy;
use Core\Model\QueryBuilder\Select;
use Core\Model\QueryBuilder\Where;

/**
 * Class QueryBuilder
 * @package Calculator\Application\DataBase
 */
class QueryBuilder
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var Select
     */
    protected $select;

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var GroupBy
     */
    protected $groupBy;

    /**
     * @var OrderBy
     */
    protected $orderBy;

    /**
     * @var Limit
     */
    protected $limit;

    /**
     * QueryBuilder constructor.
     *
     * @param string $table
     *
     * @throws \Exception
     */
    public function __construct(string $table)
    {
        $this->table      = $table;
        $this->select     = new Select($this->table);
        $this->where      = new Where();
        $this->groupBy    = new GroupBy();
        $this->orderBy    = new OrderBy();
        $this->limit      = new Limit();
    }

    /**
     * @param array $columns
     *
     * @return QueryBuilder
     */
    public function addSelect(array $columns = []): QueryBuilder
    {
        $this->select->addSelect($columns);
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $op
     * @param string $expr
     *
     * @return QueryBuilder
     */
    public function addWhere(string $key, string $value, string $op = Where::OPERATOR_AND, string $expr = '='): self
    {
        $this->where->addWhere($key, $value, $op, $expr);
        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function addGroupBy(array $columns): self
    {
        $this->groupBy->addGroupBy($columns);
        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $direction
     *
     * @return QueryBuilder
     */
    public function addOrderBy(string $column, string $direction = null): self
    {
        $this->orderBy->addOrderBy($column, $direction);
        return $this;
    }

    /**
     * @param int $limit
     *
     * @return QueryBuilder
     */
    public function addLimit(int $limit): self
    {
        $this->limit->addLimit($limit);
        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->select . $this->where . $this->groupBy . $this->orderBy . $this->limit;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getQuery();
    }
}