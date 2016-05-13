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
    
    function takeWhile(callable $pred) {
        return new Seq(function () use ($pred) {
            $idx = -1;
            
            foreach ($this as $item) {
                if (!$pred($item, ++$idx)) {
                    break;
                }
                
                yield $item;
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

    function skipWhile(callable $pred) {
        return new Seq(function () use ($pred) {
            $idx = -1;
            $started = false;
            
            foreach ($this as $item) {
                if (!$started && !$pred($item, ++$idx)) {
                    $started = true;
                }
                
                if ($started) {
                    yield $item;
                }
            }
        });
    }
    
    function reduce(callable $fn, $initialValue = null) {
        $idx = -1;
        $lastValue = $initialValue;
        
        foreach ($this as $item) {
            $lastValue = $fn($lastValue, $item, ++$idx);
        }
        
        if ($idx === -1) {
            $ret = $initialValue;
        } else {
            $ret = $lastValue;
        }
        
        return $ret;
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
       
        $idx = -1;
        
        foreach ($this as $item) {
            ++$idx;
            $fn($item, $idx);
        }
        
        return $idx;
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
    
    static function from($source) {
        $ret = null;
        
        if ($source instanceof Seq) {
            $ret = $source;
        } else if (is_array($source) || $source instanceof IteratorAggregate) {
            $ret = new Seq(function () use ($source) {
                foreach ($source as $item) {
                    yield $item;
                } 
            });
        } else {
            $ret = self::nil();
        }
        
        return $ret;
    }
    
    static function isIterable($source) {
        return (is_array($source) || $source instanceof IteratorAggregate);
    }
    
    static function nil() {
        return Seq::from([]); 
    }
    
    static function range($start, $end, $step = 1) {
        if (!is_int($start)) {
            throw new InvalidArgumentException(
                '[Seq.range] First argument $start must be an integer');
        } else if (!is_int($end)) {
            throw new InvalidArgumentException(
                '[Seq.range] Second argument $end must be an integer');
        } else if (!is_int($step) || $step === 0) {
            throw new InvalidArgumentException(
                '[Seq.range] Thrid argument $step must be an non-zero integer');
        }
        
        return new Seq(function () use ($start, $end, $step) {
            if ($start < $end && $step > 0) {
                for ($i = $start; $i < $end; $i += $step) {
                    yield $i;
                } 
            } else if ($start > $end && $step < 0) {
                for ($i = $start; $i > $end; $i += $step) {
                    yield $i;
                } 
            }
        });
    }
    
    static function iterate(array $startValues, callable $fn) {
        return new Seq(function () use ($startValues, $fn) {
            foreach ($startValues as $value) {
                yield $value;
            }
            
            $values = $startValues;

            while (true) {
                $value = call_user_func_array($fn, $values);
                array_push($values, $value);
                array_shift($values);

                yield $value;
            }
        });
    }
}
