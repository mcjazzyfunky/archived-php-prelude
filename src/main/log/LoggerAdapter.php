<?php

namespace prelude\log;

Interface LoggerAdapter {
    function getLog($domain);
    
    function getLogLevelByDomain($domain);
}