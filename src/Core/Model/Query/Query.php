<?php

namespace Core\Model\Query;

/**
 * Class Query
 * @package Core\Model\Query
 */
class Query
{
    protected $query;

    protected $where = [];

    protected $join;

    protected $customWhere;

    protected $groupBy;

    protected $orderBy;

    protected $limit;

    protected $table;

    protected $columns;

    protected $lastQuery;
}