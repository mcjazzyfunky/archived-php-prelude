<?php

namespace prelude\log;

Interface LogAdapter {
    function getLog($domain);
}