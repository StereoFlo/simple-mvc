<?php


namespace Core\Database\QueryBuilder;

/**
 * Class Where
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class Where extends AbstractPart
{
    const OPERATOR_AND = 'AND';
    const OPERATOR_OR  = 'OR';

    /**
     * @var WhereItem[]
     */
    protected $data;

    /**
     * Where constructor.
     */
    public function __construct()
    {
        $this->data = [];
        $this->result = ' WHERE ';
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $op
     * @param string $expr
     *
     * @return self
     */
    public function addWhere(string $key, string $value, string $op = self::OPERATOR_AND, string $expr = '='): self
    {
        $this->data[] = new WhereItem($key, $value, $op, $expr);

        return $this;
    }

    /**
     * @return self
     */
    public function process(): self
    {
        if (empty($this->data)) {
            $this->result = '';
            return $this;
        }

        foreach ($this->data as $key => $where) {
            if ($key !== 0) {
                $this->result .= ' ' . $where->getOperator() . ' ' . $where->getKey() . ' ' . $where->getExpr() . ' \'' . $where->getValue() . '\'';
                continue;
            }
            $this->result .= ' ' . $where->getKey() . ' ' . $where->getExpr() . ' \'' . $where->getValue() . '\'';
        }
        return $this;
    }
}