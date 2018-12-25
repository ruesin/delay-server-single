<?php

include_once __DIR__ . '/common/start.php';

$config = include ROOT_DIR . '/config/server.php';

if (!isset($argv[1])) {
    return false;
}

//process http tcp
$type = $argv[1];

if (!isset($config[$type])) {
    return false;
}

//start stop reload restart
$operate = isset($argv[2]) ? $argv[2] : 'start';

$class = new \Swover\Server($config[$type]);
$class->$operate();