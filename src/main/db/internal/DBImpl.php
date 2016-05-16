<?php

namespace prelude\db\internal;

require_once __DIR__ . '/adapters/PDOSQLiteAdapter.php';
require_once __DIR__ . '/adapters/PDOMySQLAdapter.php';
require_once __DIR__ . '/DBMultiQueryImpl.php';
require_once __DIR__ . '/DBQueryImpl.php';
require_once __DIR__ . '/../DB.php';
require_once __DIR__ . '/../DBException.php';
require_once __DIR__ . '/../DBQuery.php';
require_once __DIR__ . '/../../util/ValueObject.php';
require_once __DIR__ . '/../../util/Seq.php';

use InvalidArgumentException;
use PDO;
use prelude\db\DB;
use prelude\db\DBException;
use prelude\db\DBQuery;
use prelude\db\internal\adapters\PDOSQLiteAdapter;
use prelude\db\internal\adaptes\PDOMySQAdapter;
use prelude\util\Seq;
use prelude\util\ValueObject;

class DBImpl implements DB {
    private $params;
    private $adapter;
    
    private static $registeredDBs = [];
    
    function __construct(array $params) {
        $this->params = $params;
        $dsn = $this->params['dsn'];
        
        if (substr($dsn, 0, 7) === 'sqlite:') {
            $this->adapter = new PDOSQLiteAdapter($params);
        } else if (substr($dsn, 0, 6) === 'mysql:') {
            $this->adapter = new PDOMySQLAdapter($params);
        } else {
            throw new DBException("Incompatible database DSN '$dsn'");
        }
    }
    
    function getParams() {
        return $this->params;
    }

    function query($query, $bindings = null, $limit = null, $offset = 0) {
        return new DBQueryImpl($this->adapter, $query, $bindings, $limit, $offset);
    }

    function multiQuery($query, $bindings = null, $forceTransaction = false) {
        return new DBMultiQueryImpl($this->adapter, $query, $bindings, $forceTransaction);
    }
    
    function runTransaction(callable $transaction) {
        $this->adapter->runTransaction($transaction);
    }
}

