<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/Files.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use InvalidArgumentException;
use prelude\util\Seq;

class TextWriter {
    private $filename;
    private $context;
    
    private function __construct($filename, array $context = null, &$targetString = null) {
        $this->filename = $filename;
        $this->context = $context;
        $this->targetString = &$targetString;
    }
    
    function writeFull($text) {
        if ($this->targetString !== null) {
            $this->targetString = $text;
        } else {
            if (!is_string($text)) {
                throw new InvalidArgumentException(
                    '[FileWriter#writeFull] First argument $text must be a string');
            }
            
            $filename = $this->filename;
            $dir = dirname($filename);
            $context = $this->context;
            
            // TODO !!!!
            /*
            if (!is_dir($dir)) {
                Files::makeDir($dir, 0777, true);
            }
            */
                    
            $result = $context === null
                ? @file_put_contents(
                    $filename,
                    $text,
                    0)
                : @file_put_contents(
                    $filename,
                    $text,
                    0,
                    $context);
            
            if ($result === false) {
                $message = error_get_last()['message'];
                throw new IOException($message);
            }
        }
    }
    
    function writeLines(Seq $lines, $lineSeparator = "\r\n") {
        if (!is_string($lineSeparator)) {
            throw new InvalidArgumentException(
                '[FileWriter#writeLines] Second argument $lineSeparator must be a string');
        }
        
        $filename = $this->filename;
        $context = $this->context;
                
        $fhandle = $context === null
            ? @fopen(
                $filename,
                'wb',
                false)
            : @fopen(
                $filename,
                'wb',
                false,
                $context);
        
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
    
    static function fromFile($file, array $context = null) {
         if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[FileWriter.fromFile] First argument $file must either '
                . 'be a string or a File object');
        }
        
        $filename = is_string($file) ? $file : $file->getPath();

        return new self($filename, $context);
    }
    
    static function fromString(&$targetString) {
        if (!is_string($targetString)) {
            throw new InvalidArgumentException(
                '[TextWriter.fromString] First argument $targetString '
                . 'must be a string');
        }
        
        return new self(null, null, $targetString);
    }
}
