<?php

namespace prelude\db\internal;

require_once __DIR__ . '/../../util/Seq.php';

use prelude\util\Seq;

interface DBAdapter {
    function process($query, Seq $bindings = null, $forceTransaction = false);
    function fetch($query, $bindings = null, $limit = null, $offset = 0);
    function runTransaction(callable $transaction);
    function runIsolated(callable $action);
}
