<?php

namespace prelude\db;

require_once __DIR__ . '/../../main/db/Database.php';

use PHPUnit_Framework_TestCase;

error_reporting(E_ALL);

class DatabaseTest extends PHPUnit_Framework_TestCase {
    function testRun() {
        Database::registerDB('shop', new Database('sqlite::memory:'));
        
        $database = Database::getDB('shop');
    
        //$db = Database::getDB('shop');
        
        // $db->execute('create table user (id primary key, firstName, lastName, city, country)');
        // $db->execute("insert into user values (111, 'John', 'Doe', 'Seattle', 'USA')");
        // $db->execute("insert into user values (222, 'Jane', 'Whatever', 'London', 'UK')");
        
        $database
            ->query('create table user (id primary key, firstName, lastName, city, country)')
            ->execute();
            
        $database
            ->query("insert into user values (111, 'John', 'Doe', 'Seattle', 'USA')")
            ->execute();
            
        $database
            ->query("insert into user values (222, 'Jane', 'Whatever', 'London', 'UK')")
            ->execute();

        
        
        //$users = $db->getSeqOfVOs('select * from user');

        $users =
            $database
                ->query('select * from user')
                ->fetchSeqOfVOs();    

        print "\nKnown users by ID:\n\n";
            
        foreach ($users as $user) {
            printf(
                "%d: %s %s - %s, %s\n",
                $user->id,
                $user->firstName,
                $user->lastName,
                $user->city,
                $user->country
            );
        }
        
        flush();
    }
}
