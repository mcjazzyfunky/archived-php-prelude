<?php

namespace prelude\db;

interface DBQueryBuilder extends DBExecutor {
    function select($selectClause);

    function where($whereClause, $bindings = null);

    function groupBy($groupByClause, $bindings = null);

    function having($havingClause, $bindings = null);

    function limit($limit);

    function offset($offset);
}
