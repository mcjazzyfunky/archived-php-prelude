<?php

namespace prelude\db;

class Database {
    private $dsn;
    private $username;
    private $password;
    private $options;
    private $connection;
    
    private static registeredDBs = [];
    
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
        $conn = $this->getConnection();
    }
    
    function getOne($query, $bindings = null) {
        
    }
    
    function getRow($query, $bindings = null, $offset = 0) {
        
    }
    
    function getRows($query, $bindings = null, $count = null, $offset = 0) {
        
    }
    
    function getSeqOfRows($query, $bindings = null, $count = null, $offset = 0) {
        
    }

    function getRec($query, $bindings = null, $offset = 0) {
        
    }
    
    function getRecs($query, $bindings = null, $count = null, $offset = 0) {
        
    }
    
    function getSeqOfRecs($query, $bindings = null, $count = null, $offset = 0) {
        
    }
    
    function getVO($query, $bindings = null, $count = null, $offset = 0) {
        
    }
    
    function getVOs($query, $bindings = null, $count = null, $offset = 0) {
        
    }
    
    function getSeqOfVOs($query, $bindings = null, $count = null, $offset = 0) {
        
    }

    function getFirsts($query, $binings = null, $count = null, $offset = 0) {
        
    }

    function getSeqOfFirsts($query, $bindings = null, $count = null, $offset = 0) {
        
    }

    function getMap($query, $binings = null, $count = null, $offset = 0) {
    }
    
    // --- static methods -------------------------------------------

    static function registerDB($alias, $dsn, $username = null, $password = null) {
        $ret = new self($dsn, $username, $password);
        self::unregister($alias);
        self::$registeredDBs[$alias] = $ret;
        return $ret;
    }
    
    static function unregisterDB($alias) {
        if (isset(self::$registeredDBs[$alias])) {
            $db = self::$registeredDBs[$alias];
            $db->close();
            unset(self::$registeredDBs[$alias]);
        }
    }

    static function getDB($alias) {
        $ret = null;
    
        if (!isset(self::$registeredDBs[$alias])) {
            throw new DatabaseException(
                "[Database.getDB] Database '$alias' is not registered!");
        } else {
            $ret = self::registeredDBs[$alias];
        }
    
        return $ret;
    }

    // --- private methods ------------------------------------------
    
    private function getConnection() {
        $ret = $this->connection;
        
        if ($ret === null) {
            $this->connection = new PDO(
                $this->dsn,
                $this->username,
                $this->password,
                $this->options);
                
            $ret = $this->connection;
        }

        return $ret;
    }
    
    private runQuery($query, $bindings = null) {
        if (!is_string($query)) {
            throw new IllegalArgumentException(
                "[Database#runQuery] Second argument $query must be a string";
        } else if (!is_scalar($bindings) && !is_array($bindings)) {
            throw new IllegalArgumentException(
                "[Database#runQuery] Second argument $bindings must be a scalar or an array";   
        }
        
        
        
        $stmt = $db->prepare('SELECT * FROM tbl WHERE id = :id');
        
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $result = $stmt->execute();
    }

    private function limitQueryByLimitClause($qry, $count, $offset) {
        $ret = $qry;
        
        if ($count !== null || $offset > 0) {
            if ($count === null) {
                // TODO
                $count = "2000000000";
            } elseif ($count <= 0) {
                $count = 0;
            }
      
            $offset = max(0, (int)$offset);
      
           $qryLower = strtolower($qry);
      
            if (strpos($qryLower, 'limit') === false && strpos($qryLower, 'union') === false) {
                $ret = "$qry\nlimit $count offset $offset";
            } else {
                $ret = "select ___.*\nfrom(\n$qry\n) as ___\nlimit $count offset $offset";
            }
        }

        return $ret;
    }
}


        /*
        
        // returns integer value
        $db->getOne(
            'select count(*) from users where country=:0 and city=:1',
            [$country, $city]);
        
        // returns integer value
        $db->getOne(
            'select count(*) from users where country=:country and city=:city',
            ['country' => $country, 'city' => $city]);
        

        // returns something like [[111, 'John', 'Doe'], [222, 'Jane', 'Whoever']]
        $db->getRows(
            'select id, firstName, lastName from users where country=:0 and city=:1',
            [$country, $city]);

        // returns something like
        // [['id' => 111, 'firstName' => 'John', 'lastName' => 'Doe'],
        //  ['id' => 222, 'firstName' => 'Jane', 'lastName' => 'Whoever']]
        $db->getRecs(
            'select id, firstName, lastName from users where country=:0 and city=:1',
            [$country, $city]);

        // returns something like
        // LazySequence(
        //    ['id' => 111, 'firstName' => 'John', 'lastName' => 'Doe'],
        //    ['id' => 222, 'firstName' => 'Jane', 'lastName' => 'Whoever'])
        $db->getSeqOfRecs(
            'select * from users where country=:0 and city=:1',
            [$country, $city]);
        
        */
        