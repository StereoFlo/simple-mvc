<?php


namespace Core\Model\Query\Where;


class Where
{
    /**
     * @var array
     */
    protected $where = [];

    /**
     * @var string
     */
    protected $paramTypeList = '';

    /**
     * @var string
     */
    protected $whereTypeList = '';

    /**
     * @var string
     */
    protected $query = '';

    /**
     * Where constructor.
     *
     * @param string $query
     */
    public function __construct(string $query)
    {
        $this->query = $query;
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    public function addWhere(string $key, $value): self
    {
        $this->where[$key] = $value;
        return $this;
    }

    /**
     * @param array $tableData
     * @param bool  $hasTableData
     *
     * @return Where
     */
    public function init(array $tableData, bool $hasTableData): self
    {
        if (empty($this->where)) {
            return $this;
        }
        // if update data was passed, filter through and create the SQL query, accordingly.
        if ($hasTableData) {
            $pos = \strpos($this->query, 'UPDATE');
            if (false !== $pos) {
                foreach ($tableData as $prop => $value) {
                    // determines what data type the item is, for binding purposes.
                    $this->paramTypeList .= $this->determineType($value);

                    // prepares the reset of the SQL query.
                    $this->query .= ($prop . ' = ?, ');
                }
                $this->query = \rtrim($this->query, ', ');
            }
        }

        //Prepare the where portion of the query
        $this->query .= ' WHERE ';
        foreach ($this->where as $column => $value) {
            $comparison = ' = ? ';
            if (\is_array($value)) {
                // if the value is an array, then this isn't a basic = comparison
                $key = \key($value);
                $val = $value[$key];
                switch (\strtolower($key)) {
                    case 'in':
                        $comparison = ' IN (';
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->whereTypeList .= $this->determineType($v);
                        }
                        $comparison = \rtrim($comparison, ',') . ' ) ';
                        break;
                    case 'between':
                        $comparison = ' BETWEEN ? AND ? ';
                        $this->whereTypeList .= $this->determineType($val[0]);
                        $this->whereTypeList .= $this->determineType($val[1]);
                        break;
                    default:
                        $comparison = ' ' . $key . ' ? ';
                        $this->whereTypeList .= $this->determineType($val);
                }
            } else {
                $this->whereTypeList .= $this->determineType($value);
            }
            $this->query .= ($column . $comparison . ' AND ');
        }
        $this->query = \rtrim($this->query, ' AND ');
        return $this;
    }

    /**
     * @param string $item
     *
     * @return string
     */
    protected function determineType(string $item): string
    {
        switch (\gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }
}