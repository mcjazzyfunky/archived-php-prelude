<?php

namespace prelude\db;

require_once __DIR__ . '/DBException.php';
require_once __DIR__ . '/DBQuery.php';
require_once __DIR__ . '/../util/ValueObject.php';
require_once __DIR__ . '/../util/Seq.php';

use InvalidArgumentException;
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
    
    function fetchDsn() {
        return $this->dsn;
    }
    
    function fetchUsername() {
        return $this->username;
    }
    
    function fetchPassword() {
        return $this->password;
    }
    
    function fetchOptions() {
        return $this->options;
    }
    
    function query($query, $bindings = null, $limit = null, $offset = 0) {
        return new DBQuery($this, $query, $bindings, $limit, $offset);
    }
    
    function execute($query, $bindings = null) {
        $this->fetchSeqOfRecs($query, $bindings)
            ->take(1)
            ->force();
    }
    
    function fetchSingle($query, $bindings = null, $offset = 0) {
        return @$this->fetchRow($query, $bindings, 1, $offset)[0];
    }
    
    function fetchRow($query, $bindings = null, $limit = null, $offset = 0) {
        return @$this->fetchRows($query, $bindings, $limit, $offset)[0];
    }
    
    function fetchRows($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfRows($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function fetchSeqOfRows($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfRecs($query, $bindings, $limit, $offset)
            ->map(function ($rec) {
                return array_values($rec);
            });
    }

    function fetchRec($query, $bindings = null, $offset = 0) {
        return @$this->fetchRecs($query, $bindings, $limit, $offset)[0];
    }
    
    function fetchRecs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfRecs($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function fetchSeqOfRecs($query, $bindings = null, $limit = null, $offset = 0) {
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
    
    function fetchVO($query, $bindings = null, $limit = null, $offset = 0) {
        return @$this->fetchVOs($query, $bindings, $limit, $offset)[0];
    }
    
    function fetchVOs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfVOs($query, $bindings, $limit, $offset)
            ->toArray(); 
    }
    
    function fetchSeqOfVOs($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfRecs($query, $bindings, $limit, $offset)
            ->map(function ($rec) {
                return new ValueObject($rec);
            });
    }

    function fetchSingles($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfSingles($query, $bindings, $limit, $offset)
            ->toArray();
    }

    function fetchSeqOfSingles($query, $bindings = null, $limit = null, $offset = 0) {
        return $this->fetchSeqOfRows($query, $bindings, $limit, $offset)
            ->map(function ($row) {
                return @$row[0];
            });
    }

    function fetchMap($query, $binings = null, $limit = null, $offset = 0) {
        $ret = [];
        
        $rows = $this->fetchSeqOfRows($query, $bindings, $limit, $offset);
        
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
    
    function getConnection() {
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
