<?php

namespace prelude\db;

interface DBMultiExecutor {
    function forceTransaction($forceTransaction);

    function process();
}
