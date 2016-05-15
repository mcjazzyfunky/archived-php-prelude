<?php

namespace prelude\db;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../util/Seq.php';
require_once __DIR__ . '/../util/ValueObject.php';

use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;

interface DBMultiQuery {
    function bindMany($bindings);
    
    function forceTransaction($forceTransaction);

    function process();
}
