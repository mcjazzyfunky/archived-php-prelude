<?php

namespace prelude\util;

require_once(__DIR__ . '/../../main/util/Seq.php');

error_reporting(E_ALL);

class SeqTest extends \PHPUnit_Framework_TestCase {
    function testMethodFilter() {
        $arr = Seq::range(1, 10)
            ->filter(function ($n) {
                return $n % 2 == 0;
            })
            ->toArray();

        $this->assertEquals($arr, [2, 4, 6, 8]);
    }
    
    function testMethodMap() {
        $arr = Seq::range(1, 4)
            ->map(function ($n) {
                return $n * 2;
            })
            ->toArray();
            
        $this->assertEquals($arr, [2, 4, 6]);
    }
    
    function testMethodTake() {
        $arr = Seq::range(1, 100)
            ->take(4)
            ->toArray();
            
        $this->assertEquals($arr, [1, 2, 3, 4]);
    }
    
    function testMethodSkip() {
        $arr = Seq::range(1, 6)
            ->skip(3)
            ->toArray();
        
        $this->assertEquals($arr, [4, 5]);
    }
    
    function testMethodCount() {
        $count = Seq::range(1, 100)
            ->count();
            
        $this->assertEquals($count, 99);
    }
    
    function testMethodEach() {
        $arr = [];
        
        Seq::range(1, 4)
            ->each(function ($item) use (&$arr) {
                array_push($arr, $item);
            });
            
        $this->assertEquals($arr, [1, 2, 3]);
    }
}
