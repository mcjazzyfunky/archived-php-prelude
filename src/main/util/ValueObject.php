<?php

namespace prelude\util;

use ArrayAccess;
use Countable;

class ValueObject implements ArrayAccess, Countable {
    protected static $propInfoByName = array();
    protected static $propInfoByIdx = array();
    protected $propMap = array();

    function __construct($props = null) {
        if (is_array($props)) {
            foreach ($props as $k => $v) {
                $this->__set($k, $v);
            }
        }
    }

    public function __get($propName) {
        $ret = null;
        $id = mb_strtolower(str_replace('_', '', $propName));
        $propInfo = @self::$propInfoByName[$id];

        if ($propInfo === null) {
            throw new IllegalArgumentException("[ValueObject#__get] Tried to read unknown property '$propName'!");
        }

        $ret = $this->propMap[$propInfo[0]];
        return $ret;
    }

    public function __set($propName, $value) {
        $propNameLowerCase = mb_strtolower($propName);
        $id = str_replace('_', '', $propNameLowerCase);
        $info = @self::$propInfoByName[$id];

        if ($info === null) {
            $propNameCamelCase = $propName;

            if (strpos($propName, '_') !== false) {
                    $propNameCamelCase = implode('', array_map('ucfirst', explode('_', $propNameLowerCase)));
            }

            $nameCamelCase = mb_strtolower($propNameCamelCase{0}) . mb_substr($propNameCamelCase, 1);
            $idx = count(self::$propInfoByIdx);
            $info = array($idx, $nameCamelCase);
            self::$propInfoByName[$id] = &$info;
            self::$propInfoByName[mb_strtoupper($id)] = &$info;
            self::$propInfoByName[$propName] = &$info;
            self::$propInfoByName[$propNameLowerCase] = &$info;
            self::$propInfoByName[mb_strtoupper($propName)] = &$info;
            self::$propInfoByName[$propNameCamelCase] = &$info;
            self::$propInfoByIdx[] = &$info;
        }

        $ref =& $this->propMap[$info[0]];
        $ref = $value;
    }

    function toArray() {
        $ret = array();

        foreach ($this->propMap as $idx => $v) {
            $propInfo = self::$propInfoByIdx[$idx];
            $ret[$propInfo[1]] = $this->propMap[$idx];
        }

        return $ret;
    }

    public function offsetExists($propName) {
        $ret = isset($this->propMap[@self::$propInfoByName[strtolower($propName)][0]]);
        return $ret;
    }

    public function offsetGet($propName) {
        $ret = $this->__get($propName);
        return $ret;
    }

    public function offsetSet($propName, $value) {
        $this->__set($propName, $value);
    }

    public function offsetUnset($propName) {
        $idx = @self::$propInfoByName[$propName][0];

        if ($idx !== null) {
            unset($this->propMap[$idx]);
        }
    }

    public function count() {
        $ret = count($this->propMap);
        return $ret;
    }
}
