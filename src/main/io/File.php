<?php

namespace prelude\io;

require_once(__DIR__ . '/IOException.php');

use InvalidArgumentException;

final class File {
    private $path;
    
    private function __construct($path) {
        $this->path = $path;
    }
    
    function getPath() {
        return $this->path;
    }
    
    function getFileName() {
        return Files::getFileName($this->path);
    }
    
    function getParentFile() {
        return Files::getParentFile($this->path);
    }
    
    function getParentPath() {
        return Files::getParentPath($this->path);
    }
    
    function isFile() {
        return Files::isFile($this->path);
    }

    function isDir() {
        return Files::isDir($this->path);
    }

    function isLink() {
        return Files::isLink($this->path);
    }
    
    function getSize() {
        return Files::getSize($this->path);
    }
    
    function isAbsolute() {
        return Files::isAbsolute($this->path);
    }

    function getCreationTime() {
        return Files::getCreationTime($this->path);
    }
    
    function getLastModifiedTime() {
        return Files::getLastModifiedTime($this->path);
    }
    
    function getLastAccessTime() {
        return Files::getLastAccessTime($this->path);
    }
    
    function getSecondsSinceCreation() {
        return Files::getSecondsSinceCreation($this->path);
    }

    function getSecondsSinceLastModified() {
        return Files::getSecondsSinceLastModified($this->path);
    }
    
    function getSecondsSinceLastAccess() {
        return Files::getSecondsSinceLastAccess($this->path);
    }
    
    static function from($path) {
         if (!is_string($path) && !($path instanceof self)) {
            throw new InvalidArgumentException(
                '[File.from] First argument $path must be a string');
        }
        
        return new self($path);
    }
}
