<?php

namespace prelude\io;

require_once(__DIR__ . '/IOException.php');

class File {
    private $path;
    
    function __construct($path) {
         if (!is_string($path)) {
            throw new InvalidArgumentException(
                '[File.__construct] First argument $path must be a string');
        }

        $this->path = $path;
    }
    
    function isFile() {
        return is_file($this->path);
    }

    function isDir() {
        return is_dir($this->path);
    }

    function isLink() {
        return is_link($this->path);
    }
    
    function getSize() {
        return filesize($this->path);
    }
    
    function getPath() {
        return $this->path;
    }
}
