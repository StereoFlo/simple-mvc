<?php


namespace Core\Model\QueryBuilder;

/**
 * Class Select
 * @package Calculator\Application\DataBase\QueryBuilder
 */
class Select extends AbstractPart
{
    const SELECT_TEMPLATE = 'SELECT %s FROM `%s` %s';
    const REPLACE_PATTERN = '/(`)?((\w+)\((\w+)\))(`)?/i';
    const REPLACE_TEMPLATE = '$3(`$4`)';

    /**
     * @var string|null
     */
    private $table;

    /**
     * @var string
     */
    protected $tableAlias = null;

    /**
     * @var string
     */
    protected $columnsString = '';

    /**
     * Select constructor.
     *
     * @param string $table
     *
     * @throws \Exception
     */
    public function __construct(?string $table)
    {
        $this->data       = [];
        $this->table      = $table;
        $this->tableAlias = $this->getTableAlias();
    }

    /**
     * @param string $table
     *
     * @return Select
     * @throws \Exception
     */
    public function setTable(string $table): Select
    {
        $this->table = $table;
        $this->tableAlias = $this->getTableAlias();
        return $this;
    }

    /**
     * @return string
     */
    public function getTable(): ?string
    {
        return $this->table;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function addSelect(array $columns): self
    {
        if (empty($this->data) && empty($columns)) {
            return $this;
        }
        $this->data = \array_merge($this->data, $columns);
        return $this;
    }

    /**
     * @return self
     */
    public function process(): self
    {
        if (empty($this->data)) {
            $this->result = \sprintf(self::SELECT_TEMPLATE, '*', $this->table, $this->tableAlias);
            return $this;
        }
        $this->result = \sprintf(self::SELECT_TEMPLATE, $this->getColumns(), $this->table, $this->tableAlias);
        return $this;
    }

    /**
     * @return string
     */
    protected function getColumns(): string
    {
        foreach ($this->data as $column) {
            if (\preg_match('/`/', $column)) {
                $this->fillColumnsString($column);
                continue;
            }
            if (\preg_match(self::REPLACE_PATTERN, $column)) {
                $column = \preg_replace(self::REPLACE_PATTERN, self::REPLACE_TEMPLATE, $column);
                $this->fillColumnsString($column);
                continue;
            }
            $column = \preg_replace('/(\w+)/i', '`$1`', $column);
            $this->fillColumnsString($column, true);
        }
        return $this->columnsString;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTableAlias(): string
    {
        if (empty($this->tableAlias)) {
            $tmp = \str_split($this->table);
            if (empty($tmp)) {
                throw new \Exception('something is wrong');
            }
            $this->tableAlias = $tmp[0];
            return $this->tableAlias;
        }
        return $this->tableAlias;
    }

    /**
     * @param string $column
     * @param bool   $isNeedAlias
     *
     * @return Select
     */
    protected function fillColumnsString(string $column, bool $isNeedAlias = false): self
    {
        if (empty($this->columnsString)) {
            $this->columnsString = $isNeedAlias ? $this->tableAlias . '.' . $column : $column;
            return $this;
        }

        $column = $isNeedAlias ? $this->tableAlias . '.' . $column : $column;
        $this->columnsString .= ', ' . $column;
        return $this;
    }
}