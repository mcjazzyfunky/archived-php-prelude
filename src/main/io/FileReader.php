<?php

namespace prelude\io;

require_once(__DIR__ . '/FileRef.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use \IllegalArgumentException;
use \prelude\util\Seq;

class FileReader {
    private $fileRef;
    
    private function __construct(FileRef $fileRef) {
        $this->fileRef = $fileRef;
    }
    
    function readFull() {
        $filename = $this->fileRef->getFilename();
        $context = $this->fileRef->getContext();
        
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
            $context = $this->fileRef->getContext();
            
            $fhandle = $context === null
                ? @fopen(
                    $this->fileRef->getFilename(),
                    'rb',
                    false)
                : @fopen(
                    $this->fileRef->getFilename(),
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
            
            @fclose($fhandle);
        });
    }
    
    static function fromFile($filename, array $context = null) {
         if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileReader.fromFile] First argument $filename must be a string');
        }

        return new self(new FileRef($filename, $context));
    }
    
    static function fromFileRef(FileRef $fileRef) {
        return new self($fileRef);
    }
}