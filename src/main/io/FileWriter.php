<?php

namespace prelude\io;

use Closure;
use InvalidArgumentException;
use prelude\util\Seq;

final class FileWriter {
    private $openFile;
    private $append;
    
    private function __construct(Closure $openFile) {
        $this->openFile = $openFile;
        $this->append = false;
    }
    
    function append($append = true) {
        $ret = $this;
        
        if ($this->append !== $append) {
            $ret = clone $this;
            $ret->append = $append;
            return $ret;
        }
        
        return $ret;
    }
    
    function open() {
        $openFile = $this->openFile;
        return $openFile($this);
    }
    
    function writeFull($text) {
        if (!is_string($text)) {
            throw new InvalidArgumentException(
                '[FileWriter#writeFull] First argument $text must be a string');
        }
        
        $length = strlen($text);
        $stream = $this->open();
        
        try {
            $result = fwrite($stream, $text, $length);
    
            if ($result === false || $result !== $length) {
                $message = error_get_last()['message'];
                throw new IOException($message);
            }
        } finally {
            fclose($stream);
        }
        
        return $length;
    }
    
    function writeSeq(Seq $seq, $separator = "\n") {
        $itemCount = 0;
        $stream = $this->open();
        
        try {
            foreach ($seq as $item) {
                ++$itemCount;
                
                foreach ([$item, $separator] as $s) {
                    $result = fwrite($stream, $s);
                
                    if ($result === false) {
                        $message = error_get_last()['message'];
                        throw new IOException($message);
                    }
                }
            }
        } finally {
            fclose($stream);
        }
        
        return $itemCount;
    }
    
    static function fromFile($file, array $context = null) {
        $path = Files::getPath($file);
        
        $openFile = function ($FileWriter) use ($path, $context) {
            $openMode =
                $FileWriter->append
                ? 'ab'
                : 'wb';
            
            return Files::openFile($path, $openMode, $context);
        };
        
        return new self($openFile);
    }
}
