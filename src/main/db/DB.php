<?php

namespace prelude\db;

use InvalidArgumentException;
use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;


interface DB {
    function getParams();
    
    function query($query, $bindings = null, $limit = null, $offset = 0);
    
    function multiQuery($query, $bindings = null, $forceTransaction = false);
    
    function runIsolated(callable $action);

    function runTransaction(callable $transaction);

    function runIsolatedTransaction(callable $transaction);
}
