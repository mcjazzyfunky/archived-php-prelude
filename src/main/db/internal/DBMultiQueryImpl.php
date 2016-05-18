<?php

namespace prelude\db\internal;

use prelude\db\DB;
use prelude\db\DBMultiQuery;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;

class DBMultiQueryImpl implements DBMultiQuery {
    private $adapter;
    private $query;
    private $bindings;
    private $forceTransaction;

    function __construct(
        DBAdapter $adapter,
        $query,
        Seq $bindings = null,
        $forceTransaction = false) {
        
        $this->adapter = $adapter;
        $this->query = $query;
        $this->bindings = $bindings;
        $this->forceTransaction = $forceTransaction;
    }
    
    function bindMany($bindings) {
        $ret = clone $this;
        $ret->bindings = Seq::from($bindings);
        return $ret;
    }

    function forceTransaction($forceTransaction) {
        $ret = clone $this;
        $ret->forceTransaction = $forceTransaction;
        return $ret;
    }

    function process() {
        return $this->adapter->process(
            $this->query,
            $this->bindings,
            $this->forceTransaction);
    }
}
