<?php

namespace prelude\db\internal;


require_once __DIR__ . '/DBAdapter.php';
require_once __DIR__ . '/../DBException.php';
require_once __DIR__ . '/../../util/Seq.php';

use PDO;
use prelude\db\DBException;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;

abstract class AbstractPDOAdapter implements DBAdapter {
    private $dbParams;
    private $connection;
    
    public function __construct($dbParams) {
        $this->dbParams = $dbParams;
        $this->connection = null;
    }
    
    abstract protected function limitQuery($query, $limit = null, $offset = 0);

    function process($query, Seq $bindings = null, $forceTransaction = false) {
        $ret = 0;
        $qry = trim($query);
        $conn = $this->getConnection();
    
        $process = function () use ($qry, $bindings, $conn, &$ret) {
            $stmt = $conn->prepare($qry, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        
            if ($stmt === false) {
                $errorInfo = $conn->errorInfo();
                throw new DBException($errorInfo[2]);
            }
    
            try {
                foreach ($bindings as $binding) {
                    $result = $stmt->execute($binding);
                    ++$ret;
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
        
        return $ret;
    }
    
    function fetch($query, $bindings = null, $limit = null, $offset = 0) {
        $qry = $this->limitQuery($query, $limit, $offset);
        
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
    
    // --- private methods ------------------------------------------
    
    private function getConnection($forceNew = false) {
        $ret = $this->connection;
        
        if ($ret === null || $forceNew) {
            $options = @$this->dbParams['options'];
           
            // TODO: Quite sure this is a stupid idea... 
            if (empty($options[PDO::ATTR_TIMEOUT])) {
                $options[PDO::ATTR_TIMEOUT] = 30;
            }

            $this->connection = new PDO(
                $this->dbParams['dsn'],
                @$this->dbParams['username'],
                @$this->dbParams['password'],
                $options);
                
            $ret = $this->connection;
        }

        return $ret;
    }
}