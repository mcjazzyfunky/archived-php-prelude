<?php

namespace prelude\io;

require_once(__DIR__ . '/IOException.php');

class FileRef {
    private $filename;
    private $context;
    
    function __construct($filename, array $context = null) {
         if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileRef.__construct] First argument $filename must be a string');
        }

        $this->filename = $filename;
        $this->context = $context;
    }
    
    function getFilename() {
        return $this->filename;
    }
    
    function getContext() {
        return $this->context;
    }
}