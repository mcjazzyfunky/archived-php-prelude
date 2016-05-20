<?php

namespace prelude\db;

require_once __DIR__ . '/../../../include.php';

use Exception;
use PHPUnit_Framework_TestCase;
use prelude\log\Logger;
use prelude\log\Log;
use prelude\log\adapters\StreamLoggerAdapter;
use prelude\log\adapters\FileLoggerAdapter;

// this has normally to be handled in the loading script

Logger::setAdapter(new StreamLoggerAdapter(STDOUT));
//Logger::setAdapter(new FileLoggerAdapter('./test-{date}.log'));
Logger::setDefaultLogLevel(Log::INFO);

class DBTest extends PHPUnit_Framework_TestCase {
    private $log;
    
    function __construct() {
        // maybe it's better to pass the log as constructor argument
        $this->log = Logger::getLog($this);
    }
    
    function testRun() {
        // The registry pattern is not really the coolest :-(
        DBManager::registerDB('shop', ['dsn' => 'sqlite::memory:']);
       // DBManager::registerDB('shop', ['dsn' => 'mysql:host=localhost;dbname=test', 'username' => 'root']);
        
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

        $shopDB = DBManager::getDB('shop');

        $shopDB
            ->query('drop table if exists user')
            ->execute();

        $shopDB
            ->query('
                create table user
                (id integer primary key, firstName varchar(20), lastName varchar(20), city varchar(20), country varchar(20), type integer)
            ')
            ->execute();
       
       $shopDB->runTransaction(function () use ($shopDB, $newUsers) {
            $shopDB
                ->query('delete from user')
                ->execute();
                
            $this->log->info('Table has been created');
                
            $userCount = $shopDB
                ->query('
                    insert  into user values
                    (:id, :firstName, :lastName, :city, :country, :type)
                ')
                ->bindMany($newUsers)
                ->process();
                
            $this->log->info('%d users have been inserted', $userCount);
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
                ->limit(100)
                ->bind(['country' => 'USA', 'type' => 2])
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
        
        $recs = 
            $shopDB
                ->from('user')
                ->select('firstName, lastName')
                ->where('country = ?', 'UK')
                ->orderBy('lastName')
                ->limit(100)
                ->fetchRecs();
                
       // print_r($recs);
    }
}
