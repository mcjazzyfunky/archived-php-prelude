<?php

namespace prelude\db;

require_once __DIR__ . '/DBMultiQueryImpl.php';
require_once __DIR__ . '/DBQueryImpl.php';
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../DBException.php';
require_once __DIR__ . '/../DBQuery.php';
require_once __DIR__ . '/../../util/ValueObject.php';
require_once __DIR__ . '/../../util/Seq.php';

use InvalidArgumentException;
use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;

class DatabaseImpl implements Database {
    private $dsn;
    private $username;
    private $password;
    private $options;
    private $connection;
    private static $registeredDBs = [];
    
    function __construct($dsn, $username = null, $password = null, array $options = null) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->passowrd = $password;
        $this->options = $options;
        $this->connection = null;
    }
    
    function getDsn() {
        return $this->dsn;
    }
    
    function getUsername() {
        return $this->username;
    }
    
    function getPassword() {
        return $this->password;
    }
    
    function getOptions() {
        return $this->options;
    }
    
    function query($query, $bindings = null, $limit = null, $offset = 0) {
        return new DBQueryImpl($this, $query, $bindings, $limit, $offset);
    }
    
    function multiQuery($query, $bindings = null, $forceTransaction = false) {
        return new DBMultiQueryImpl($this, $query, $bindings, $forceTransaction);
    }
    
    function runTransaction(callable $transaction) {
        $conn = $this->getConnection();
        
        if ($conn->inTransaction()) {
            throw new DBException("Illegaly tried to start nexted transaction");
        }
        
        $conn->beginTransaction();
        
        try {
            $result = $transaction($this);
        
            if ($result === false) {
                $conn->rollBack();
            } else {
                $conn->commit();
            }
        } catch (throwable $t) {
            $conn->rollBack();
            throw $t;
        }
    }
    
    function process($query, Seq $bindings = null, $forceTransaction = false) {
        $qry = trim($query);
        $conn = $this->getConnection();
    
        $process = function () use ($qry, $bindings, $conn) {
            $stmt = $conn->prepare($qry, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        
            if ($stmt === false) {
                $errorInfo = $conn->errorInfo();
                throw new DBException($errorInfo[2]);
            }
    
            try {
                foreach ($bindings as $binding) {
                    $result = $stmt->execute($binding);
                }
            
                // TODO
                /*
                while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //...
                }
                */
            } finally {
                $stmt->closeCursor();
            }
        };
        
        if ($forceTransaction && !$conn->inTransaction()) {
            $this->runTransaction($process);
        } else {
            $process();
        }
    }
    
    function fetch($query, $bindings = null, $limit = null, $offset = 0) {
        $qry = self::limitQueryByLimitClause($query, $limit, $offset);
        
        return new Seq(function () use ($qry, $bindings) {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($qry, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            
            if ($stmt === false) {
                $errorInfo = $conn->errorInfo();
                throw new DBException($errorInfo[2]);
            }
            
            try {
                $result = $stmt->execute($bindings);
            
                while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    yield $rec;
                }
                
                $errorInfo = $stmt->errorInfo();
                
                if (0 + $errorInfo[0] !== 0) {
                    throw new DBException($errorInfo[2]); 
                }
            } finally {
                $stmt->closeCursor();
            }
        });
    }
    
    // --- private methods ------------------------------------------
    
    private function getConnection() {
        $ret = $this->connection;
        
        if ($ret === null) {
            $options = $this->options;
           
            // TODO: Quite sure this is a stupid idea... 
            if (empty($options[PDO::ATTR_TIMEOUT])) {
                $options[PDO::ATTR_TIMEOUT] = 30;
            }

            $this->connection = new PDO(
                $this->dsn,
                $this->username,
                $this->password,
                $options);
                
            $ret = $this->connection;
        }

        return $ret;
    }
    
    // TODO: This is currently only working if DBMS supports limit and offset clauses
    private static function limitQueryByLimitClause($qry, $limit, $offset) {
        $qry = trim($qry);
        $ret = $qry;
        
        if ($limit !== null || $offset > 0) {
            if ($limit === null) {
                // TODO
                $limit = "2000000000";
            } elseif ($limit <= 0) {
                $limit = 0;
            }
      
            $offset = max(0, (int)$offset);
      
           $qryLower = strtolower($qry);
      
            if (strpos($qryLower, 'limit') === false && strpos($qryLower, 'union') === false) {
                $ret = "$qry\nlimit $limit offset $offset";
            } else {
                $ret = "select ___.*\nfrom(\n$qry\n) as ___\nlimit $limit offset $offset";
            }
        }

        return $ret;
    }
}
