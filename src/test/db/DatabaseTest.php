<?php

namespace prelude\db;

require_once __DIR__ . '/../../main/db/Database.php';

use PHPUnit_Framework_TestCase;

error_reporting(E_ALL);

class DatabaseTest extends PHPUnit_Framework_TestCase {
    function testRun() {
        $db = new Database('sqlite::memory');
        
        $db->execute('drop table if exists user');
        $db->execute('create table user (id integer primary key, firstName varchar, lastName varchar)');
        $db->execute("insert into user values (111, 'John', 'Doe')");
        $db->execute("insert into user values (222, 'Jane', 'Whatever')");
        
        $users = $db->getSeqOfVOs('select * from user');
        print "\nKnown users by ID:\n\n";
            
        foreach ($users as $user) {
            printf("%d: %s %s\n", $user->id, $user->firstName, $user->lastName);
        }
        
        flush();
    }
}
