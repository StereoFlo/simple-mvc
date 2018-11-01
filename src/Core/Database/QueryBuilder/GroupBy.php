<?php


namespace Core\Database\QueryBuilder;

/**
 * Class GroupBy
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class GroupBy extends AbstractPart
{
    /**
     * Where constructor.
     */
    public function __construct()
    {
        $this->data = [];
        $this->result = ' GROUP BY ';
    }

    /**
     * @param array $column
     *
     * @return self
     */
    public function addGroupBy(array $column): self
    {
        $this->data = array_merge($this->data, $column);
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

        $this->result .= ' `' . \implode('`, `', $this->data) . '`';
        return $this;
    }
}