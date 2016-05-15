<?php

namespace prelude\db;

require_once __DIR__ . '/../../main/db/Database.php';

use PHPUnit_Framework_TestCase;

error_reporting(E_ALL);

class DatabaseTest extends PHPUnit_Framework_TestCase {
    function testRun() {
        Database::registerDB('shop', new Database('sqlite::memory:'));
        
        $shopDB = Database::getDB('shop');
    
        $newUsers = [[
            'id' => 1001,
            'firstName' => 'John',
            'lastName' => 'Doe',
            'city' => 'Seattle',
            'country' => 'USA',
            'type' => 1
        ], [
            'id' => 1002,
            'firstName' => 'Jimmy',
            'lastName' => 'Gym',
            'city' => 'Boston',
            'country' => 'USA',
            'type' => 1
        ], [
            'id' => 1003,
            'firstName' => 'Johnny',
            'lastName' => 'Chopper',
            'city' => 'Portland',
            'country' => 'USA',
            'type' => 2,
        ], [
            'id' => 1004,
            'firstName' => 'Jane',
            'lastName' => 'Whatever',
            'city' => 'London',
            'country' => 'UK',
            'type' => 2
        ]]; 

        $shopDB = Database::getDB('shop');

        $shopDB
            ->query('
                create table user
                (id primary key, firstName, lastName, city, country, type)
            ')
            ->execute();
       
        $shopDB->runTransaction(function () use ($shopDB, $newUsers) {
            $shopDB
                ->query('delete from user')
                ->execute();
                
            $shopDB
                ->multiQuery('
                    insert  into user values
                    (:id, :firstName, :lastName, :city, :country, :type)
                ')
                ->bindMany($newUsers)
                ->process();
        });
        
        $users =
            $shopDB
                ->query('
                    select
                        *
                    from
                        user
                    where
                        country=:country and type=:type
                ')
                ->bind(['country' => 'USA', 'type' => 2])
                ->limit(100)
                ->fetchSeqOfVOs();    

        print "\nKnown users by ID:\n\n";
            
        foreach ($users as $user) {
            printf(
                "%d: %s %s (%s) - %s, %s\n",
                $user->id,
                $user->firstName,
                $user->lastName,
                $user->type,
                $user->city,
                $user->country
            );
        }
        
        flush();
    }
}
