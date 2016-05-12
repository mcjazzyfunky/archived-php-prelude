<?php

namespace prelude\io;

require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use \IllegalArgumentException;
use \prelude\util\Seq;

class FileWriter {
    private $filename;
    
    private function __construct($filename) {
        if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileWriter.__construct] First argument $filename must be a string');
        }
        
        $this->filename = $filename;    
    }
    
    function writeFullText($text) {
        if (!is_string($text)) {
            throw new InvalidArgumentException(
                '[FileWriter#writeFullText] First argument $text must be a string');
        }
        
        $result = @file_put_contents($this->filename, $text);
        
        if ($result === false) {
            $message = error_get_last()['message'];
            throw new IOException($message);
        }
    }
    
    function writeLines(Seq $lines, $lineSeparator = "\r\n") {
        if (!is_string($lineSeparator)) {
            throw new IllegalArgumentException(
                '[FileWriter#writeLines] Second argument $lineSeparator must be a string');
        }
        
        $fhandle = @fopen($this->filename, "wb");
        
        if ($fhandle === false) {
            $message = error_get_last()['message'];
            throw new IOException($message);
        }
        
        foreach ($lines as $line) {
            foreach ([$line, $lineSeparator] as $s) {
                $result = fwrite($fhandle, $s);
            
                if ($result === false) {
                    $message = error_get_last()['message'];
                    @fclose($fhandle);
                    throw new IOException($message);
                }
            }
        }
        
        @fclose($fhandle);
    }
    
    
    static function forFile($filename) {
        if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileWriter.forFile] First argument $filename must be a string');
        }
        
        return new FileWriter($filename);
    }
}
