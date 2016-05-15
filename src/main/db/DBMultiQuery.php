<?php

namespace prelude\db;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../util/Seq.php';
require_once __DIR__ . '/../util/ValueObject.php';

use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;

class DBMultiQuery {
    private $db;
    private $query;
    private $bindings;

    function __construct(Database $db, $query, Seq $bindings = null) {
        $this->db = $db;
        $this->query = $query;
        $this->bindings = $bindings;
    }
    
    function bindMany($bindings) {
        $ret = clone $this;
        $ret->bindings = Seq::from($bindings);
        return $ret;
    }

    function process() {
        return $this->db->process(
            $this->query,
            $this->bindings);
    }
}
