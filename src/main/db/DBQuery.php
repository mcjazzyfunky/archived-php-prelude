<?php

namespace prelude\db;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../util/Seq.php';
require_once __DIR__ . '/../util/ValueObject.php';

use PDO;
use prelude\util\Seq;
use prelude\util\ValueObject;

class DBQuery {
    function __construct(Database $db, $query, $bindings = null, $limit = null, $offset = null) {
        $this->db = $db;
        $this->query = $query;
        $this->bindings = $bindings;
        $this->limit = $limit;
        $this->offset = $offset;
    }
    
    function bind($params) {
        $ret = $this->copy();
        $ret->bindings = $params;
        return $ret;
    }
    
    function limit($n) {
        $ret = $this->copy();
        $ret->limit = $n;
        return $ret;
    }

    function offset($n) {
        $ret = $this->copy();
        $ret->offset = $n;
        return $ret;
    }
    
    function execute() {
        $this->fetchRec();
    }
    
    function fetchSingle() {
        $row = $this->fetchRow();
    
        return $row === null ? null : $row[0];
    }
    
    function fetchRow() {
        $rec = $this->fetchRec();
    
        return $rec = null ? null : array_values($rec);
    }
    
    function fetchRec() {
        $arr =
            $this->fetchSeqOfRecs()
                ->take(1)
                ->toArray();
        
        return count($arr) === 0 ? null : $arr[0];
    }
    
    function fetchVO() {
        $rec = $this->fetchRec();
    
        return $rec === null ? $rec : new ValueObject($rec);
    }
    
    function fetchSingles() {
        return $this->fetchSeqOfSingles()->toArray();
    }
    
    function fetchRows() {
        return $this->fetchSeqOfRows()->toArray();
    }
    
    function fetchRecs() {
        return $this->fetchSeqOfRecs()->toArray();
    }
    
    function fetchMap() {
        $ret = [];
        
        foreach ($this->fetchSeqOfRows() as $row) {
            $ret[$row[0]] = @$row[1];
        }
        
        return $ret;
    }
    
    function fetchSeqOfSingles() {
        return $this->fetchRows()->map(function ($row) {
            return $row[0]; 
        });
    }
    
    function fetchSeqOfRows() {
        return $this->fetchRecs()->map(function ($rec) {
            return array_values($rec); 
        });
    }
    
    function fetchSeqOfRecs() {
        $qry = self::limitQueryByLimitClause($this->query, $this->limit, $this->offset);
        $bindings = $this->bindings;
        
        return new Seq(function () use ($qry, $bindings) {
            $conn = $this->db->getConnection();
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
    
    function fetchSeqOfVOs() {
        return $this->fetchSeqOfRecs()->map(function ($rec) {
            return new ValueObject($rec);
        });
    }
    
    private function copy() {
        return new DBQuery(
            $this->query,
            $this->bindings,
            $this->limit,
            $this->offset);
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
