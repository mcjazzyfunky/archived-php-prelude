<?php

namespace prelude\db;

interface DBMultiQuery {
    function bindMany($bindings);
    
    function forceTransaction($forceTransaction);

    function process();
}
