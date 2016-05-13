<?php

class CSVFormat {
    private $columns;
    private $delimiter;
    
    function __construct() {
        $this->columns = null;
        $this->delimiter = ',';
        $this->autoTrim = false;
    }
    
    function columns(array $columns) {
        $ret = $this->clone();
        $ret->columns = $columns;
        return ret;
    }

    function delimiter($delimiter) {
        $ret = $this->clone();
        $ret->delimiter = $delimiter;
        return $ret;
    }

    function autoTrim($autoTrim) {
        $ret = $this->clone();
        $ret->autoTrim = $autoTrim;
        return $ret;
    }
    
    private clone() {
        $ret = new CSVFormat();
        $ret->columns = $this->columns;
        $ret->delimiter = $this->delimiter;
        $ret->autoTrim = $this->autoTrim;
        
        return ret;
    }
}