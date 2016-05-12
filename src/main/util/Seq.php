<?php

namespace prelude\util;

use \Closure;
use \Generator;
use \InvalidArgumentException;
use \IteratorAggregate;
use \UnexpectedValueException; 

class Seq implements IteratorAggregate {
    private $generatorFunction;
    
    function __construct(Closure $generatorFunction) {
        $this->generatorFunction = $generatorFunction;
    }
    
    function filter(callable $pred) {
        return new Seq(function () use ($pred) {
            $idx = -1;
            
            foreach ($this as $item) {
                if ($pred($item, ++$idx)) {
                    yield $item;
                }
            }
        });
    }
    
    function map(callable $fn) {
        return new Seq(function () use ($fn) {
            $idx = -1;
            
            foreach ($this as $item) {
                yield $fn($item, ++$idx);
            }
        });
    }
    
    function take($n) {
        if (!is_int($n)) {
            throw new InvalidArgumentException(
                '[Seq#take] First argument $n must be an integer');
        }
        
        return new Seq(function () use ($n) {
            $idx = -1;
            
            foreach ($this as $item) {
                if (++$idx < $n) {
                    yield $item;
                } else {
                    break;
                }
            }
        });
    }
    
    function skip($n) {
        if (!is_int($n)) {
            throw new InvalidArgumentException(
                '[Seq#skip] First argument $n must be an integer');
        }
        
        return new Seq(function () use ($n) {
            $idx = -1;
            
            foreach ($this as $item) {
                if (++$idx >= $n) {
                    yield $item;
                }
            }
        });
    }
    
    function count() {
        $count = 0;
        
        foreach ($this as $item) {
            ++$count;
        }
        
        return $count;
    }
    
    function each(callable $fn) {
        if (!is_callable($fn)) {
            throw new InvalidArgumentException(
                '[Seq.each] First argument $fn must be a function');
        }
        
        foreach ($this as $item) {
            $fn($item);
        }
    }
    
    function toArray() {
        $ret = [];
        
        foreach ($this as $item) {
            array_push($ret, $item);
        }
        
        return $ret;
    }
    
    function getIterator() {
        $generatorFunction = $this->generatorFunction;
        $ret = $generatorFunction();
        
        if (!($ret instanceof Generator)) {
            throw new UnexpectedValueException(
                '[Seq#getIterator] Generator function did not really return a generator');
        }
        
        return $ret;
    }
    
    static function range($start, $end) {
        if (!is_int($start)) {
            throw new InvalidArgumentException(
                '[Seq.range] First argument $start must be an integer');
        } else if (!is_int($end)) {
            throw new InvalidArgumentException(
                '[Seq.range] Second argument $end must be an integer');
        }
        
        return new Seq(function () use ($start, $end) {
            for ($i = $start; $i < $end; ++$i) {
                yield $i;
            } 
        });
    }
}
