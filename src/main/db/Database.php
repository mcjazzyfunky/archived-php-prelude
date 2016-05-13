<?php

namespace prelude\db;

require_once __DIR__ . '/DBException.php';
require_once __DIR__ . '/../util/ValueObject.php';
require_once __DIR__ . '/../util/Seq.php';

use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;


class Database {
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
    
    function execute($query, $bindings = null) {
        $this->getSeqOfRecs($query, $bindings)
            ->take(1)
            ->force();
    }
    
    function getSingle($query, $bindings = null, $offset = 0) {
        return @$this->getRow($query, $bindings, 1, $offset)[0];
    }
    
    function getRow($query, $bindings = null, $limit = null, $offset = 0) {
        return @$this->getRows($query, $bindings, $limit, $offset)[0];
    }
    
    function getRows($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfRows($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function getSeqOfRows($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfRecs($query, $bindings, $limit, $offset)
            ->map(function ($rec) {
                return array_values($rec);
            });
    }

    function getRec($query, $bindings = null, $offset = 0) {
        return @$this->getRecs($query, $bindings, $limit, $offset)[0];
    }
    
    function getRecs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfRecs($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function getSeqOfRecs($query, $bindings = null, $limit = null, $offset = 0) {
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
    
    function getVO($query, $bindings = null, $limit = null, $offset = 0) {
        return @$this->getVOs($query, $bindings, $limit, $offset)[0];
    }
    
    function getVOs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfVOs($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function getSeqOfVOs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfRecs($query, $bindings, $limit, $offset)
            ->map(function ($rec) {
                return new ValueObject($rec);
            });
    }

    function getSingles($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfSingles($query, $bindings, $limit, $offset)
            ->toArray();
    }

    function getSeqOfSingles($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->getSeqOfRows($query, $bindings, $limit, $offset)
            ->map(function ($row) {
                return @$row[0];
            });
    }

    function getMap($query, $binings = null, $limit = null, $offset = 0) {
        $ret = [];
        
        $rows = $this->getSeqOfRows($query, $bindings, $limit, $offset);
        
        foreach ($seq as $row) {
            $ret[@$row[0]] = @$row[1];
        }
            
        return $ret;
    }
    
    // --- public static methods ------------------------------------

    static function registerDB($alias, Database $db) {
        self::unregisterDB($alias);
        self::$registeredDBs[$alias] = $db;
    }
    
    static function unregisterDB($alias) {
        if (isset(self::$registeredDBs[$alias])) {
            $db = self::$registeredDBs[$alias];
            $db->connection = null;
            unset(self::$registeredDBs[$alias]);
        }
    }

    static function getDB($alias) {
        $ret = null;
    
        if (!isset(self::$registeredDBs[$alias])) {
            throw new DBException(
                "[Database.getDB] Database '$alias' is not registered!");
        } else {
            $ret = self::$registeredDBs[$alias];
        }
    
        return $ret;
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
