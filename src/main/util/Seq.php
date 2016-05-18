<?php

namespace prelude\util;

use Closure;
use Generator;
use InvalidArgumentException;
use IteratorAggregate;
use UnexpectedValueException; 

final class Seq implements IteratorAggregate {
    private $generatorFunction;
    private $args;
    
    private function __construct(Closure $generatorFunction, array $args = null) {
        $this->generatorFunction = $generatorFunction;
        $this->args = $args;
    }
    
    function filter(callable $pred) {
        return new self(function () use ($pred) {
            $idx = -1;
            
            foreach ($this as $item) {
                if ($pred($item, ++$idx)) {
                    yield $item;
                }
            }
        });
    }
    
    function reject(callable $pred) {
        return $this->filter(function ($item, $idx) {
            return !$pred($item, $idx);
        });
    }
    
    function rejectNulls() {
        return $this->filter(function ($item) {
            return $item !== null;
        });
    }

    function map(callable $fn) {
        return new self(function () use ($fn) {
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
        
        return new self(function () use ($n) {
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
        return new self(function () use ($pred) {
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
        
        return new self(function () use ($n) {
            $idx = -1;
            
            foreach ($this as $item) {
                if (++$idx >= $n) {
                    yield $item;
                }
            }
        });
    }

    function skipWhile(callable $pred) {
        return new self(function () use ($pred) {
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
    
    function flatten() {
        return new self(function () {
            foreach ($this as $item) {
                foreach (Seq::from($item) as $subitem) {
                    yield $subitem;
                }
            } 
        });
    }
    
    function flatMap(callable $fn) {
        return $this->map($fn)->flatten();
    }
    
    function prepend($item) {
        return Seq::concat(Seq::of($item), $this);
    }
    
    function prependMany($items) {
        return Seq::concat($items, $this);
    }

    function append($item) {
        return Seq::concat($this, Seq::of($item));
    }
    
    function appendMany($items) {
        return Seq::concat($this, $items);
    }
    
    function sort($order = null) {
        if ($order !== null && !is_integer($order) && !is_callable($order)) {
            throw new InvalidArgumentException(
                '[Seq#sort] First argument $order must either be an integer '
                . 'or a callabel or null');
        }
        
        return new self(function () use ($order) {
            $arr = $this->toArray();
            
            if ($order === null) {
                sort($arr);
            } else if (is_integer($order)) {
                sort($arr, $order);
            } else {
                usort($arr, $order);
            }
            
            foreach ($arr as $item) {
                yield $item;
            }
        });
    }
    
    function peek(callable $action) {
        return new self(function () use ($action) {
            $idx = -1; 
            
            foreach ($this as $item) {
                $action($this, -$idx);
                yield $item;
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
    
    function max(callable $comparator = null, $defaultValue = null) {
        $ret = $defaultValue;
        $isFirst = true;
        
        foreach ($this as $item) {
            if ($isFirst) {
                $ret = $item;
                $isFirst = false;
            } else {
                if ($comparator === null) {
                    if ($item > $ret) {
                        $ret = $item;
                    }
                } else {
                    $result = $comparator($item, $ret);
                    
                    if ($result >= 1) {
                        $ret = $item;
                    }
                }             
            }
        }
        
        return $ret;
    }

    function min(callable $comparator = null, $defaultValue = null) {
        $ret = $defaultValue;
        $isFirst = true;
        
        foreach ($this as $item) {
            if ($isFirst) {
                $ret = $item;
                $isFirst = false;
            } else {
                if ($comparator === null) {
                    if ($item < $ret) {
                        $ret = $item;
                    }
                } else {
                    $result = $comparator($ret, $item);
                    
                    if ($result >= 1) {
                        $ret = $item;
                    }
                }             
            }
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
    
    function force() {
        return Seq::from($this->toArray());
    }
    
    function getIterator() {
        $generatorFunction = $this->generatorFunction;
        
        if ($this->args === null) {
            $ret = $generatorFunction();
        } else {
            $ret = call_user_func_array($generatorFunction, $this->args);
        }
        
        if (!($ret instanceof Generator)) {
            throw new UnexpectedValueException(
                '[Seq#getIterator] Generator function did not really return a generator');
        }
        
        return $ret;
    }
    
    static function of($item) {
        return new self(function () use ($item) {
            yield $item; 
        });
    }
    
    static function create(Closure $generatorFunction, array $args = null) {
        return new self($generatorFunction, $args);
    }
    
    static function from($source) {
        $ret = null;
        
        if ($source instanceof Seq) {
            $ret = $source;
        } else if (is_array($source) || $source instanceof IteratorAggregate) {
            $ret = new self(function () use ($source) {
                foreach ($source as $item) {
                    yield $item;
                } 
            });
        } else if ($source instanceof Closure) {
            $ret = new self($source);
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
        
        return new self(function () use ($start, $end, $step) {
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
        return new self(function () use ($startValues, $fn) {
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
    
    static function repeat($item, $count = null) {
        return new self(function () use ($item, $count) {
            $idx = -1;
            
            while ($count === null || ++$idx < $count) {
                yield $item;
            }
        });
    }
    
    static function cycle($items, $count = null) {
        $seq = Seq::from($items);

        return new self(function () use ($seq, $count) {
            $idx = -1;
            
            while ($count === null || ++$idx < $count) {
                foreach ($seq as $item) {
                    yield $item;
                }
            }
        });
    }
    
    static function concat($iterable1, $iterable2) {
        return self::concatMany([$iterable1, $iterable2]);
    }
     
    static function concatMany($iterable) {
        $seq = Seq::from($iterable);
        
        return new self(function () use ($seq) {
            foreach ($seq as $items) {
                foreach(Seq::from($items) as $item) {
                    yield $item;
                }
            } 
        });
    }

    static function zip($iterable1, $iterable2, callable $fn = null) {
        $seq1 = Seq::from($iterable1);
        $seq2 = Seq::from($iterable2);
        
        return new self(function () use ($seq1, $seq2, $fn) {
            $generator1 = null;
            $generator2 = null;

            try {
                $generator1 = $seq1->getIterator();
                $generator2 = $seq2->getIterator();
                
                while ($generator1->valid() && $generator2->valid()) {
                    $item1 = $generator1->current();
                    $item2 = $generator2->current();
                    
                    $generator1->next();
                    $generator2->next();
                    
                    if ($fn === null) {
                        yield [$item1, $item2];
                    } else {
                        yield $fn($item1, $item2);
                    }
                }
            } finally {
                $generator1 = null;
                $generator2 = null;
            }
        });
    }

    static function zipMany($iterable, callable $fn = null) {
        $iterables =
            is_array($iterable)
            ? $iterable
            : Seq::from($iterable)->toArray();
        
        return new self(function () use ($iterables, $fn) {
            $iterators = [];
            
            try {
                foreach ($iterables as $iterable) {
                    $iterators[] = Seq::from($iterable)->getIterator();
                }
                
                $idx = -1;
                
                while (true) {
                    foreach ($iterators as $iterator) {
                        if (!$iterator->valid()) {
                             break(2);
                        }
                    }

                    $items = [];                    
                    
                    foreach ($iterators as $iterator) {
                        $items[] = $iterator->current();
                        $iterator->next();
                    }                
                    
                    if ($fn === null) {
                        yield $items;
                    } else {
                        yield call_user_func_array($fn, $items);
                    }
                }
            } finally {
                $iterators = null;
            }
        });
    }
}
