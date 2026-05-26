<?php

function cli_echo($info = NULL, $flag = NULL) {
    if(!$info || !$flag) {
        return false;
    }
    $now_datetime = date('Y-m-d H:i:s');
    echo "[$flag] {$now_datetime}: {$info}\n";
}

function parseArgv(&$ip, &$port, &$time) {
    $ip = $_SERVER['argv'][1];
    $port = $_SERVER['argv'][2];
    $time = $_SERVER['argv'][3];
}