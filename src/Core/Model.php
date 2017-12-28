<?php

namespace Core;

use App\Utils;

class Model extends \mysqli
{
    /**
     * The SQL query to be prepared and executed
     *
     * @var string
     */
    protected $query;

    /**
     * An array that holds where joins
     *
     * @var array
     */
    protected $join = [];

    /**
     * An array that holds where conditions 'fieldname' => 'value'
     *
     * @var array
     */
    protected $where = [];

    /**
     * A string that holds a custom where string
     *
     * @var string
     */
    protected $customWhere = '';

    /**
     * Dynamic type list for where condition values
     *
     * @var string
     */
    protected $whereTypeList = '';

    /**
     * Dynamic type list for order by condition value
     */
    protected $orderBy = [];

    /**
     * Dynamic type list for group by condition value
     */
    protected $groupBy = [];

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * Dynamic type list for table data values
     *
     * @var string
     */
    protected $paramTypeList = '';

    /**
     * Dynamic array that holds a combination of where condition/table data value types and parameter references
     *
     * @var array
     */
    protected $bindParams = ['']; // Create the empty 0 index

    /**
     * @var string
     */
    protected $db_prefix;

    /**
     * @var \mysqli_stmt
     */
    protected $stmt;

    /**
     * @var
     */
    protected $db_result;

    /**
     * Db results output can be set to an array or object
     *
     * @var
     */
    protected $output = 'array';

    /**
     * @var
     */
    protected $lastError;

    /**
     * @var
     */
    protected $lastQuery;

    /**
     * @var bool
     */
    protected $inTransaction = false;

    /**
     * @var string
     */
    public $sqlstate = '';

    /**
     * Model constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $config = Config::getConfig('main', 'database');
        $host = Utils::getProperty($config, 'host');
        $dbUser = Utils::getProperty($config, 'user');
        $dbPassword = Utils::getProperty($config, 'password');
        $dbName = Utils::getProperty($config, 'basename');
        $port = Utils::getProperty($config, 'port', ini_get('mysqli.default_port'));
        $this->db_prefix = Utils::getProperty($config, 'prefix', '');
        parent::__construct($host, $dbUser, $dbPassword, $dbName, $port, $this->db_prefix);
        if ($this->connect_errno) {
            throw new \Exception(("Failed to connect to MySQL: (" . $this->connect_errno . ") " . $this->connect_error));
        }
        //$this->set_charset('utf8');
        //$this->stmt = $this->init();
    }

    /**
     * Reset states after an execution
     *
     * @return self
     */
    protected function reset(): self
    {
        $this->where = [];
        $this->customWhere = '';
        $this->join = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->bindParams = ['']; // Create the empty 0 index
        $this->query = '';
        $this->whereTypeList = '';
        $this->paramTypeList = '';
        $this->output = null;
        $this->limit = 0;
        $this->offset = 0;
        return $this;
    }

    /**
     * Pass in a raw query and an array containing the parameters to bind to the prepared statement.
     *
     * @param string $query Contains a user-provided query.
     * @param array $bindParams All variables to bind to the SQL statement.
     *
     * @return array Contains the returned rows from the query.
     * @throws \Exception
     */
    public function rawQuery(string $query, $bindParams = null): array
    {
        $this->query = \filter_var($query, FILTER_SANITIZE_STRING);
        $this->query = \html_entity_decode($this->query);

        if (\is_array($bindParams)) {
            $params = ['']; // Create the empty 0 index
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->determineType($val);
                \array_push($params, $bindParams[$prop]);
            }
            \call_user_func_array([$this->stmt, 'bind_param'], $this->refValues($params));
        }
        $this->processQuery();
        return $this->db_result;
    }

    /**
     *
     * @param string $query Contains a user-provided select query.
     * @param int $resultMode
     *
     * @return array Contains the returned rows from the query.
     * @throws \Exception
     */
    public function query($query, $resultMode = MYSQLI_STORE_RESULT): array
    {
        $this->query = \filter_var($query, FILTER_SANITIZE_STRING);
        $this->query = \html_entity_decode($this->query);
        $this->processQuery();
        return $this->db_result;
    }

    /**
     * A convenient SELECT * function.
     *
     * @param string $tableName The name of the database table to work with.
     * @param string|array $columns The database columns to select.
     *
     * @return array Contains the returned rows from the select query.
     * @throws \Exception
     */
    public function get(string $tableName, $columns = '*'): array
    {
        $columns = \is_array($columns) ? $columns : $this->multiExplode($columns, ',');
        $columns = \implode(', ', $columns);
        $this->query = 'SELECT ' . $columns . ' FROM ' . $this->db_prefix . $tableName;
        $this->processQuery();
        return $this->db_result;
    }

    /**
     * A convenient SELECT * function to get one column.
     *
     * @param string $tableName The name of the database table to work with.
     * @param string|array $columns The database columns to select.
     *
     * @return array Contains the returned rows from the select query.
     * @throws \Exception
     */
    public function getOne(string $tableName, $columns = '*')
    {
        $column = \is_array($columns) ? \implode(', ', $columns) : $columns;
        $this->query = 'SELECT ' . $column . ' FROM ' . $this->db_prefix . $tableName;
        $this->limit(1);
        $this->processQuery();
        if (!is_array($this->db_result) || empty($this->db_result)) {
            return [];
        }
        foreach ($this->db_result as $result) {
            return $result;
        }
        return [];
    }

    /**
     * A convenient SELECT * function to get one column.
     *
     * @param string $tableName The name of the database table to work with.
     * @param string|array $column The database column to select.
     *
     * @return array Contains the returned rows from the select query.
     * @throws \Exception
     */
    public function getCol(string $tableName, $column)
    {
        $column = \is_array($column) ? \array_shift($column) : $column;
        $this->get($tableName, $column);
        $new_array = [];
        if (\is_array($this->db_result) && !empty($this->db_result)) {
            // Extract the column values
            foreach ($this->db_result as $result) {
                $new_array[] = $result->{$column};
            }
        }
        $this->db_result = $new_array;
        return $this->db_result;
    }

    /**
     * A convenient SELECT * function to get one record.
     *
     * @param string $tableName The name of the database table to work with.
     * @param string|array $columns The database columns to select.
     *
     * @return array Contains the returned rows from the select query.
     * @throws \Exception
     */
    public function getVar(string $tableName, $columns = '*')
    {
        $column = \is_array($columns) ? \implode(', ', $columns) : $columns;
        $this->query = 'SELECT ' . $column . ' FROM ' . $this->db_prefix . $tableName;
        $this->limit(1);
        $this->output('array');
        $this->processQuery();
        if (\is_array($this->db_result) && !empty($this->db_result)) {
            $this->db_result = $this->db_result[0][$column];
        }
        return $this->db_result;
    }

    /**
     *
     * @param string $tableName The name of the table.
     * @param array $tableData Data containing information for inserting into the DB.
     *
     * @return boolean Boolean indicating whether the insert query was completed succesfully.
     * @throws \Exception
     */
    public function insert(string $tableName, array $tableData = [])
    {
        $this->query = 'INSERT INTO ' . $this->db_prefix . $tableName;
        $this->processQuery($tableData);
        return $this->db_result;
    }

    /**
     * Update query. Be sure to first call the "where" method.
     *
     * @param string $tableName The name of the database table to work with.
     * @param array $tableData Array of data to update the desired row.
     *
     * @return boolean
     * @throws \Exception
     */
    public function update(string $tableName, $tableData)
    {
        $this->query = 'UPDATE ' . $this->db_prefix . $tableName . ' SET ';
        $this->processQuery($tableData);
        return $this->db_result;
    }

    /**
     * Delete query. Call the "where" method first.
     *
     * @param string $tableName The name of the database table to work with.
     *
     * @return boolean Indicates success. 0 or 1.
     * @throws \Exception
     */
    public function delete(string $tableName)
    {
        $this->query = 'DELETE FROM ' . $this->db_prefix . $tableName;
        $this->processQuery();
        return $this->db_result;

    }

    /**
     * @param array $tableData
     *
     * @return Model
     * @throws \Exception
     */
    public function processQuery(array $tableData = []): self
    {
        $this->buildQuery($tableData);
        $this->setLastQuery($this->query);

        //execute query
        if (!$this->stmt->execute()) {
            $this->throwError();
        }
        $this->dynamicBindResults();
        $this->reset();
        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) WHERE statements for SQL queries.
     *
     * @uses $MySqliDb->where('id', 7)->where('title', 'MyTitle');
     *
     * @param string $whereProp The name of the database field.
     * @param mixed $whereValue The value of the database field.
     *
     * @return self
     */
    public function where(string $whereProp, $whereValue): self
    {
        $this->where[$whereProp] = $whereValue;
        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function customWhere($where): self
    {
        $this->customWhere = $where;
        return $this;
    }

    /**
     * This method allows you to concatenate joins for the final SQL statement.
     *
     * @uses $this->join('table1', 'field1 <> field2', 'LEFT')
     *
     * @param string $joinTable     The name of the table.
     * @param string $joinCondition the condition.
     * @param string $joinType      'LEFT', 'INNER' etc.
     *
     * @return Model
     * @throws \Exception
     */
    public function join(string $joinTable, string $joinCondition, string $joinType = ''): self
    {
        $allowedTypes = ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'];
        $joinType = \strtoupper(trim($joinType));
        $joinTable = filter_var($joinTable, FILTER_SANITIZE_STRING);

        if ($joinType && !\in_array($joinType, $allowedTypes))
            throw new \Exception('Wrong JOIN type: ' . $joinType);

        $this->join[$joinType . " JOIN " . $this->db_prefix . $joinTable] = $joinCondition;

        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) ORDER BY statements for SQL queries.
     *
     * @uses $MySqliDb->orderBy('id', 'desc')->orderBy('name', 'desc');
     *
     * @param string $orderByField     The name of the database field.
     * @param string $orderByDirection Order direction.
     *
     * @return Model
     * @throws \Exception
     */
    public function orderBy(string $orderByField, string $orderByDirection = 'DESC'): self
    {
        $allowedDirection = ["ASC", "DESC"];
        $orderByDirection = \strtoupper(\trim($orderByDirection));
        $orderByField = \filter_var($orderByField, FILTER_SANITIZE_STRING);

        if (empty($orderByDirection) || !in_array($orderByDirection, $allowedDirection))
            throw new \Exception('Wrong order direction: ' . $orderByDirection);

        $this->orderBy[$orderByField] = $orderByDirection;
        return $this;
    }

    /**
     * This method allows you to specify multiple (method chaining optional) GROUP BY statements for SQL queries.
     *
     * @uses $this->groupBy('name');
     *
     * @param string $groupByField The name of the database field.
     *
     * @return self
     */
    public function groupBy($groupByField): self
    {
        $groupByField = \filter_var($groupByField, FILTER_SANITIZE_STRING);
        $this->groupBy[] = $groupByField;
        return $this;
    }

    /**
     * @param $limit
     *
     * @return $this
     */
    public function limit($limit): self
    {
        $limit = \filter_var($limit, FILTER_SANITIZE_NUMBER_INT);
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset = 0): self
    {
        $this->offset = ($offset > 0) ? $offset : 0;
        return $this;
    }

    /**
     * @param string $output
     *
     * @return $this
     */
    public function output(string $output): self
    {
        if (!in_array($output, ['object', 'array'])) {
            $output = 'array';
        }
        $this->output = $output;
        return $this;
    }

    /**
     * This methods returns the ID of the last inserted item
     *
     * @return integer The last inserted item ID.
     */
    public function getInsertId(): int
    {
        return $this->insert_id;
    }

    /**
     * Escape harmful characters which might affect a query.
     *
     * @param string $str The string to escape.
     *
     * @return string The escaped string.
     */
    public function escape(string $str): string
    {
        return $this->real_escape_string($str);
    }

    /**
     * This method is needed for prepared statements. They require
     * the data type of the field to be bound with "i" s", etc.
     * This function takes the input, determines what type it is,
     * and then updates the param_type.
     *
     * @param mixed $item Input to determine the type.
     *
     * @return string The joined parameter types.
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

    /**
     * Abstraction method that will compile the WHERE statement,
     * any passed update data, and the desired rows.
     * It then builds the SQL query.
     *
     * @param array $tableData Should contain an array of data for updating the database.
     *
     * @return Model Returns the $this->stmt object.
     * @throws \Exception
     */
    protected function buildQuery(array $tableData = []): self
    {
        $hasTableData = \is_array($tableData) && !empty($tableData);
        $hasConditional = !empty($this->where);

        // Did the user call the "join" method?
        $this->makeJoin();
        $this->makeWhere($tableData, $hasTableData);
        $this->makeCustomWhere();
        $this->makeGroupBy();
        $this->makeOrderBy();

        if ($hasTableData) {
            $this->prepareInsert($tableData);
        }
        $this->makeLimit();


        // Prepare query
        $this->stmt = $this->prepare($this->query);
        if (!$this->stmt) {
            $this->throwError();
        }

        // Prepare table data bind parameters
        if ($hasTableData) {
            $this->bindParams[0] = $this->paramTypeList;
            foreach ($tableData as $prop => $val) {
                \array_push($this->bindParams, $tableData[$prop]);
            }
        }
        // Prepare where condition bind parameters
        if ($hasConditional) {
            if ($this->where) {
                $this->bindParams[0] .= $this->whereTypeList;
                foreach ($this->where as $prop => $val) {
                    if (!\is_array($val)) {
                        \array_push($this->bindParams, $this->where[$prop]);
                        continue;
                    }
                    // if val is an array, this is not a basic = comparison operator
                    $key = \key($val);
                    $vals = $val[$key];
                    if (\is_array($vals)) {
                        // if vals is an array, this comparison operator takes more than one parameter
                        foreach ($vals as $k => $v) {
                            \array_push($this->bindParams, $this->where[$prop][$key][$k]);
                        }
                    } else {
                        // otherwise this comparison operator takes only one parameter
                        \array_push($this->bindParams, $this->where[$prop][$key]);
                    }
                }
            }
        }

        // Bind parameters to statement
        if ($hasTableData || $hasConditional) {
            \call_user_func_array([$this->stmt, 'bind_param'], $this->refValues($this->bindParams));
        }
        return $this;
    }

    /**
     * This helper method takes care of prepared statements bind_result method when the number of variables to pass is unknown.
     *
     * @return array|object The results of the SQL fetch.
     */
    protected function dynamicBindResults()
    {
        $parameters = [];
        $this->db_result = [];

        $meta = $this->stmt->result_metadata();
        // if $meta is false yet sqlstate is true, there's no sql error but the query is
        // most likely an update/insert/delete which doesn't produce any results
        if (!$meta && $this->stmt->sqlstate) {
            if (false !== \strpos($this->query, 'SELECT')) {
                //it was a select statement that produced no results, so we return an empty result set
                $this->db_result = [];
            } elseif (false !== \strpos($this->query, 'UPDATE')) {
                //return the number of affected rows if any, otherwise return true/false for success
                $this->db_result = ($this->stmt->affected_rows > 0) ? $this->stmt->affected_rows : $this->db_result;
            } elseif (false !== \strpos($this->query, 'INSERT')) {
                //return the insert_id if available, otherwise return true/false for success
                $this->db_result = ($this->stmt->insert_id > 0) ? $this->stmt->insert_id : $this->db_result;
            } else {
                //there were no errors so we return true
                $this->db_result = true;
            }
            return $this;
        }

        $row = [];
        while ($field = $meta->fetch_field()) {
            $row[$field->name] = null;
            $parameters[] = &$row[$field->name];
        }

        \call_user_func_array([$this->stmt, 'bind_result'], $parameters);

        while ($this->stmt->fetch()) {
            if ($this->output === 'array') {
                //returns an array of records as associative arrays
                $x = [];
                foreach ($row as $key => $val) {
                    $x[$key] = $val;
                }
                \array_push($this->db_result, $x);
            } else {
                //returns an array of records as objects
                $x = new \stdClass();
                foreach ($row as $key => $val) {
                    $x->{$key} = $val;
                }
                \array_push($this->db_result, $x);
            }

        }
        return $this;
    }

    /**
     * Close connection
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param array $arr
     * @return array
     */
    protected function refValues($arr)
    {
        //Reference is required for PHP 5.3+
        if (\strnatcmp(\phpversion(), '5.3') >= 0) {
            $refs = [];
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    /**
     * @return mixed
     */
    public function beginTransaction()
    {
        if ($ret = $this->autocommit(false)) {
            $this->inTransaction = true;
        } else {
            $this->inTransaction = false;
        }
        return $ret;
    }

    /**
     * @return bool
     */
    public function commitTransaction()
    {
        if (!$this->inTransaction) {
            return false;
        }
        $ret = $this->commit();
        $this->autocommit(true);
        $this->inTransaction = false;
        return $ret;
    }

    /**
     * @return bool
     */
    public function rollbackTransaction()
    {
        if (!$this->inTransaction) {
            return false;
        }
        $ret = $this->rollback();
        $this->autocommit(true);
        $this->inTransaction = false;
        return $ret;
    }

    /**
     * @param mixed $lastQuery
     */
    public function setLastQuery($lastQuery)
    {
        $this->lastQuery = $lastQuery;
    }

    /**
     * Method returns last query
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    /**
     * @param mixed $lastError
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;
    }

    /**
     * Method returns last mysql error
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param       $string
     * @param array $deliminators
     *
     * @return array
     */
    public function multiExplode($string, $deliminators = [])
    {
        if (empty($deliminators)) {
            $deliminators = array(",", ".", "|", ":", "_");
        }

        //replace all deliminators with a single one
        $string = \str_replace($deliminators, $deliminators[0], $string);
        $array = \explode($deliminators[0], $string);
        $clean = array();
        foreach ($array as $r) {
            $clean[] = \trim($r);
        }
        return $clean;
    }

    /**
     * @return self
     */
    protected function makeJoin(): self
    {
        if (empty($this->join)) {
            return $this;
        }
        foreach ($this->join as $prop => $value) {
            $this->query .= " " . $prop . " ON " . $value;
        }
        return $this;
    }

    /**
     * @return self
     */
    protected function makeCustomWhere(): self
    {
        if (empty($this->customWhere)) {
            return $this;
        }
        //is this the only "where"?
        $this->query .= (!empty($this->where)) ? ' AND ' : ' WHERE ';
        $this->query .= $this->customWhere;
        return $this;
    }

    /**
     * @return $this
     */
    protected function makeGroupBy(): self
    {
        // Did the user call the "groupBy" method?
        if (empty($this->groupBy)) {
            return $this;
        }
        $this->query .= " GROUP BY ";
        foreach ($this->groupBy as $key => $value) {
            // prepares the reset of the SQL query.
            $this->query .= $value . ", ";
        }
        $this->query = \rtrim($this->query, ', ') . " ";
        return $this;
    }

    /**
     * @return self
     */
    protected function makeOrderBy(): self
    {
        // Did the user call the "orderBy" method?
        if (empty ($this->orderBy)) {
            return $this;
        }
        $this->query .= " ORDER BY ";
        foreach ($this->orderBy as $prop => $value) {
            // prepares the reset of the SQL query.
            $this->query .= $prop . " " . $value . ", ";
        }
        $this->query = \rtrim($this->query, ', ') . " ";
        return $this;
    }

    /**
     * @return self
     */
    protected function makeLimit(): self
    {
        // Did the user call the "limit" method?
        if (empty($this->limit)) {
            return $this;
        }
        if (empty($this->offset)) {
            $this->query .= ' LIMIT ' . $this->limit;
            return $this;
        }
        $this->query .= ' LIMIT ' . $this->offset . ',' . $this->limit;
        return $this;
    }

    /**
     * @param $tableData
     * @param $hasTableData
     * @return self
     */
    protected function makeWhere($tableData, $hasTableData): self
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
        return  $this;
    }

    /**
     * @param array $tableData
     * @return self
     */
    protected function prepareInsert(array $tableData): self
    {
        $pos = \strpos($this->query, 'INSERT');
        if (!$pos) {
            return $this;
        }

        $keys = \array_keys($tableData);
        $values = \array_values($tableData);
        $num = \count($keys);

        // wrap values in quotes
        foreach ($values as $key => $val) {
            $values[$key] = "'{$val}'";
            $this->paramTypeList .= $this->determineType($val);
        }

        $this->query .= '(' . \implode($keys, ', ') . ')';
        $this->query .= ' VALUES(';
        while ($num !== 0) {
            $this->query .= '?, ';
            $num--;
        }
        $this->query = \rtrim($this->query, ', ');
        $this->query .= ')';

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function throwError()
    {
        $this->setLastError($this->sqlstate . ' ' . $this->error);
        $this->db_result = false;
        throw new \Exception("Problem preparing query ($this->query) " . $this->sqlstate . ' ' . $this->error . ' ' . print_r($this->stmt->error_list, true));
    }


}