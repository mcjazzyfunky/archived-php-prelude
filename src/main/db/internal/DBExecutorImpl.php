<?php

namespace prelude\db\internal;

use PDO;
use prelude\db\DB;
use prelude\db\DBExecutor;
use prelude\db\internal\DBAdapter;
use prelude\util\Seq;
use prelude\util\DynObject;

class DBExecutorImpl implements DBExecutor {
    protected $db;
    protected $bindings;
    protected $query;
    protected $limit;
    protected $offset;

    function __construct(DBAdapter $adapter, $query, $bindings = null, $limit = null, $offset = null) {
        $this->adapter = $adapter;
        $this->query = $query;
        $this->bindings = $bindings;
        $this->limit = $limit;
        $this->offset = $offset;
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
    
        return $rec === null ? $rec : DynObject::from($rec);
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
        return $this->adapter->fetch(
            $this->query,
            $this->bindings,
            $this->limit,
            $this->offset);
    }
    
    function fetchSeqOfVOs() {
        return $this->fetchSeqOfRecs()->map(function ($rec) {
            return DynObject::from($rec);
        });
    }
}
