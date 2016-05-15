<?php

namespace prelude\db;

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../DBMultiQuery.php';
require_once __DIR__ . '/../../util/Seq.php';

use prelude\util\Seq;

class DBMultiQueryImpl implements DBMultiQuery {
    private $db;
    private $query;
    private $bindings;
    private $forceTransaction;

    function __construct(
        Database $db,
        $query,
        Seq $bindings = null,
        $forceTransaction = false) {
        
        $this->db = $db;
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
        return $this->db->process(
            $this->query,
            $this->bindings,
            $this->forceTransaction);
    }
}
