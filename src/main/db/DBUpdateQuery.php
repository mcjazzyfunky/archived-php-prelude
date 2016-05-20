<?php

namespace prelude\db;

interface DBUpdateQuery {
    function where($whereClause, $bindings);
    
    function set(array $modifications);

    function execute();
}
