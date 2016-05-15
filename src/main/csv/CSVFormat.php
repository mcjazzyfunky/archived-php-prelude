<?php

class CSVFormat {
    private $columns;
    private $delimiter;
    private $recordSeparator;
    
    function __construct() {
        $this->columns = null;
        $this->delimiter = ',';
        $this->recordSeparator = "\n";
        $this->autoTrim = false;
        $this->escapeCharacter = null;
        $this->quoteCharacter = '"';
    }
    
    function columns(array $columns) {
        $ret = clone $this;
        $ret->columns = $columns;
        return ret;
    }

    function delimiter($delimiter) {
        $ret = clone $this;
        $ret->delimiter = $delimiter;
        return $ret;
    }

    function autoTrim($autoTrim) {
        $ret = clone $this;
        $ret->autoTrim = $autoTrim;
        return $ret;
    }
}