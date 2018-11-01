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
     * @param string|null $table
     *
     * @throws \Exception
     */
    public function __construct(string $table = null)
    {
        $this->table   = $table;
        $this->select  = new Select($this->table);
        $this->where   = new Where();
        $this->groupBy = new GroupBy();
        $this->orderBy = new OrderBy();
        $this->limit   = new Limit();
    }

    /**
     * @param string $table
     *
     * @return QueryBuilder
     */
    public function setTable(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $columns
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function addSelect(array $columns = []): QueryBuilder
    {
        $this->checkTableSet();
        if (empty($this->select->getTable())) {
            $this->select->setTable($this->table);
        }
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
     * @throws \Exception
     */
    public function addWhere(string $key, string $value, string $op = Where::OPERATOR_AND, string $expr = '='): self
    {
        $this->checkTableSet();
        $this->where->addWhere($key, $value, $op, $expr);
        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     * @throws \Exception
     */
    public function addGroupBy(array $columns): self
    {
        $this->checkTableSet();
        $this->groupBy->addGroupBy($columns);
        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $direction
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function addOrderBy(string $column, string $direction = null): self
    {
        $this->checkTableSet();
        $this->orderBy->addOrderBy($column, $direction);
        return $this;
    }

    /**
     * @param int $limit
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function addLimit(int $limit): self
    {
        $this->checkTableSet();
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

    /**
     * checkTableSet
     * @throws \Exception
     */
    protected function checkTableSet(): void
    {
        if (empty($this->table)) {
            throw new \Exception('table must be set');
        }
    }
}