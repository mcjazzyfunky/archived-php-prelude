<?php

namespace prelude\util;

use ArrayAccess;
use Countable;
use InvalidArgumentException;

final class ValueObject implements ArrayAccess, Countable {
    protected $propMap = array();

    private function __construct($props = null) {
        if (is_array($props)) {
            foreach ($props as $k => $v) {
                $this->__set($k, $v);
            }
        }
    }

    public function __get($propName) {
        if (!is_string($propName)) {
            throw new InvalidArgumentException(
                '[ValueObject#__get] First argument $propName must be a string');
        }
        
        if (!array_key_exists($propName, $this->propMap)) {
            throw new InvalidArgumentException(
                "[ValueObject#__get] Tried to read unknown property '$propName'");
        }

        return $this->propMap[$propName];
    }

    public function __set($propName, $value) {
        if (!is_string($propName)) {
            throw new InvalidArgumentException(
                '[ValueObject#__set] First argument $propName must be a string');
        }
        
        $this->propMap[$propName] = $value;
    }

    function toArray() {
        return $this->propMap();
    }

    public function offsetExists($propName) {
        return array_key_exists($this->propMap, $propName);
    }

    public function offsetGet($propName) {
        $ret = $this->__get($propName);
        return $ret;
    }

    public function offsetSet($propName, $value) {
        $this->__set($propName, $value);
    }

    public function offsetUnset($propName) {
        unset($this->propMap[$propName]);
    }

    public function count() {
        $ret = count($this->propMap);
        return $ret;
    }
    
    static function from($props) {
        return new self($props);
    }
}
