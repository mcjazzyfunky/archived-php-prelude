<?php

namespace prelude\db\internal;

use prelude\db\DB;
use prelude\db\DBMultiExecutor;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;

class DBMultiExecutorImpl implements DBMultiExecutor {
    private $adapter;
    private $query;
    private $bindings;
    private $forceTransaction;

    function __construct(
        DBAdapter $adapter,
        $query,
        $bindings) {
        
        $this->adapter = $adapter;
        $this->query = $query;
        $this->bindings = $bindings;
        $this->forceTransaction = false;
    }
    
    function forceTransaction($forceTransaction) {
        $ret = this;
        
        if ($forceTransaction !== $this->forceTransaction) {
            $ret = clone $this;
            $ret->forceTransaction = $forceTransaction;
        }

        return $ret;
    }

    function execute() {
        return $this->adapter->process(
            $this->query,
            Seq::from($this->bindings),
            $this->forceTransaction);
    }
}
