<?php

namespace prelude\db;

require_once __DIR__ . '/../../main/db/DBManager.php';
require_once __DIR__ . '/../../main/log/Logger.php';
require_once __DIR__ . '/../../main/log/Log.php';
require_once __DIR__ . '/../../main/log/adapters/StreamLogAdapter.php';

use PHPUnit_Framework_TestCase;
use prelude\log\Logger;
use prelude\log\Log;
use prelude\log\adapters\StreamLogAdapter;

// this has normally to be handled in the loading script
error_reporting(E_ALL);
Logger::setAdapter(new StreamLogAdapter(STDOUT));
Logger::setDefaultLogLevel(Log::INFO);

class DBTest extends PHPUnit_Framework_TestCase {
    private $log;
    
    function __construct() {
        // normally it's better to pass the log as constructor argument
        $this->log = Logger::getLog($this);
    }
    
    function testRun() {
        // The registry pattern is not really the coolest :-(
        DBManager::registerDB('shop', ['dsn' => 'sqlite::memory:']);
        
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
            ->query('
                create table user
                (id primary key, firstName, lastName, city, country, type)
            ')
            ->execute();
       
        $shopDB->runTransaction(function () use ($shopDB, $newUsers) {
            $shopDB
                ->query('delete from user')
                ->execute();
                
            $this->log->info('Table has been created');
                
            $userCount = $shopDB
                ->multiQuery('
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
        
        $this->log->info('Finished successfully');
        flush();
    }
}
