<?php

namespace prelude\util;

require_once __DIR__ . '/../../../include.php';

use PHPUnit_Framework_TestCase;

class SeqTest extends PHPUnit_Framework_TestCase {
    function testRun() {
        $user = DynObject::from([
            'id' => 111,
            'firstName' => 'John',
            'lastName' => 'Doe'
        ]);
        
        $user->city = 'Seattle';
        $user->country = 'USA';
        
        print "$user->id: $user->firstName $user->lastName, $user->city $user->country";
        // Prints out: 111 John Doe, Seattle USA
    }
}
