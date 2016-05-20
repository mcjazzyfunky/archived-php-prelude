<?php

namespace prelude\db\internal;

use PDO;
use prelude\db\DB;
use prelude\db\DBQuery;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;
use prelude\util\DynObject;

class DBQueryImpl extends DBExecutorImpl implements DBQuery {
    function __construct(DBAdapter $adapter, $query) {
        parent::__construct($adapter, $query);
    }
    
    function limit($n) {
        $ret = clone $this;
        $ret->limit = $n;
        return $ret;
    }

    function offset($n) {
        $ret = clone $this;
        $ret->offset = $n;
        return $ret;
    }
    
    function bind($params) {
        return new DBExecutorImpl(
            $this->adapter,
            $this->query,
            $params,
            $this->limit,
            $this->offset);
    }
    
    function bindMany($bindings) {
        return new DBMultiExecutorImpl(
            $this->adapter,
            $this->query,
            $bindings,
            $this->limit,
            $this->offset);
    }
}
