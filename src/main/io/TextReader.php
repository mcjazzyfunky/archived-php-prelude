<?php

namespace prelude\io;

require_once(__DIR__ . '/File.php');
require_once(__DIR__ . '/IOException.php');
require_once(__DIR__ . '/../util/Seq.php');

use InvalidArgumentException;
use prelude\util\Seq;

class TextReader {
    private $filename;
    private $context;
    private $text;
    
    private function __construct($filename, array $context = null, $text = null) {
        $this->filename = $filename;
        $this->context = $context;
        $this->text = $text;
    }
    
    function readFull() {
        $ret = null;
        
        if ($this->text !== null) {
            $ret = $this->text;
        } else {
            $filename = $this->filename;
            $context = $this->context;
            
            $ret = $context === null
                ? @file_get_contents($filename)
                : @file_get_contens($filename, false, $context);
            
            if ($ret === false) {
                $message = error_get_last()['message'];
                throw new IOException($message);
            }
        }

        return $ret;
    }
    
    function readLines() {
        $ret = null;
        
        if ($this->text !== null) {
            $ret = new Seq(function () {
                // Needs more temporary space but is way faster then preg_split
                $lines = explode("\n",
                    str_replace(["\r\n", "\n\r", "\r"], "\n", $this->text));
                    
                foreach ($lines as $line) {
                    yield $line;
                }
            });
        } else {
            $ret = new Seq(function() {
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
        
        return $ret;
    }
    
    static function fromFile($file, array $context = null) {
         if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[FileReader.fromFile] First argument $file must either '
                . ' be a string or a File object');
        }
        
        $filename = is_string($file) ? $file : $file->getPath();
        return new self($filename, $context, null);
    }
    
    static function fromString($text) {
         if (!is_string($text)) {
            throw new InvalidArgumentException(
                '[FileReader.fromString] First argument $text must be a string');
        }
        
        return new self(null, null, $text);
    } 
}