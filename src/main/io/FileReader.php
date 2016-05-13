<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use IllegalArgumentException;
use prelude\util\Seq;

class FileReader {
    private $filename;
    private $context;
    
    private function __construct($filename, $context = null) {
        $this->filename = $filename;
        $this->context = $context;
    }
    
    function readFull() {
        $filename = $this->filename;
        $context = $this->context;
        
        $ret = $context === null
            ? @file_get_contents($filename)
            : @file_get_contens($filename, false, $context);
        
        if ($ret === false) {
            $message = error_get_last()['message'];
            throw new IOException($message);
        }

        return $ret;
    }
    
    function readLines() {
        return new Seq(function() {
            $filename = $this->filename;
            $context = $this->context;
            
            try {
                $fhandle = $context === null
                    ? @fopen(
                        $filename,
                        'rb',
                        false)
                    : @fopen(
                        $filename,
                        'rb',
                        false,
                        $context);
                
                if ($fhandle === false) {
                    $message = error_get_last()['message'];
                    throw new IOException($message);
                }
                
                while (($line = @fgets($fhandle)) !== false) {
                    $length = strlen($line);
                    
                    while ($length > 0
                        && ($line[$length - 1] === "\r" || $line[$length -1] === "\n")) {
                    
                        --$length;
                    }
                    
                    
                    yield substr($line, 0, $length);
                }
                
                if (!feof($fhandle)) {
                    $message = error_get_last()['message'];
                    @fclose($fhandle);
                    throw new IOException($message);
                }
            } finally {
                @fclose($fhandle);
            }
        });
    }
    
    static function fromFilename($filename, array $context = null) {
         if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileReader.fromFilename] First argument $filename must be a string');
        }

        return new self($filename, $context);
    }
    
    static function fromFile(File $file) {
        return new self($file->getPath(), null);
    }
}