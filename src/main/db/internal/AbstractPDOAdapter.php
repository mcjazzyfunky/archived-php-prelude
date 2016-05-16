<?php

namespace prelude\db\internal;


require_once __DIR__ . '/DBAdapter.php';
require_once __DIR__ . '/../DBException.php';
require_once __DIR__ . '/../../util/Seq.php';

use PDO;
use PDOException;
use prelude\db\DBException;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;

abstract class AbstractPDOAdapter implements DBAdapter {
    private $dbParams;
    private $connection;
    private $isolatedConnection;
    
    public function __construct($dbParams) {
        $this->dbParams = $dbParams;
        $this->connection = null;
        $this->isolatedConnection = null;
    }
    
    abstract protected function limitQuery($query, $limit = null, $offset = 0);

    function process($query, Seq $bindings = null, $forceTransaction = false) {
        $ret = 0;
        $qry = trim($query);
        $conn = $this->getConnection();
    
        $process = function () use ($qry, $bindings, $conn, &$ret) {
            try {
                $stmt = $conn->prepare($qry, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
                
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
            } catch (PDOException $e) {
                throw new DBExcetion($e->getMessage(), $e->getCode(), $e);
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
            try {
                $conn = $this->getConnection();
                $stmt = $conn->prepare($qry, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            
                $result = $stmt->execute($bindings);

                while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    yield $rec;
                }
            } catch (PDOException $e) {
                throw new DBException($e->getMessage(), $e->getCode(), $e);
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
    
    function runIsolated(callable $action) {
        if ($this->isolatedConnection !== null) {
            throw new DBException("Nesting of isolated actions are not allowed");
        }
        
        $this->isolatedConnection = $this->getConnection(true);
        
        try {
            $action();
        } finally {
            $this->isolatedConnection = null;
        }
    }
    
    // --- private methods ------------------------------------------
    
    private function getConnection($forceNew = false) {
        $ret = $this->isolatedConnection;
        
        if ($ret === null) {
            $ret = $this->connection;
        }
        
        if ($ret === null || $forceNew) {
            $options = @$this->dbParams['options'];
           
            // TODO: Quite sure this is a stupid idea... 
            if (empty($options[PDO::ATTR_TIMEOUT])) {
                $options[PDO::ATTR_TIMEOUT] = 30;
            }
            
            $options[PDO::ATTR_EMULATE_PREPARES] = false;
            $options[PDO::ATTR_STRINGIFY_FETCHES] = false;
            $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

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