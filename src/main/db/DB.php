<?php

namespace prelude\db;

require_once __DIR__ . '/DBException.php';
require_once __DIR__ . '/DBQuery.php';
require_once __DIR__ . '/DBMultiQuery.php';
require_once __DIR__ . '/../util/ValueObject.php';
require_once __DIR__ . '/../util/Seq.php';

use InvalidArgumentException;
use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;


interface DB {
    function getParams();
    
    function query($query, $bindings = null, $limit = null, $offset = 0);
    
    function multiQuery($query, $bindings = null, $forceTransaction = false);
    
    function runTransaction(callable $transaction);
    
    function process($query, Seq $bindings = null, $forceTransaction = false);
    
    function fetch($query, $bindings = null, $limit = null, $offset = 0);
}
