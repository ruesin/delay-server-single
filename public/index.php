<?php
include_once dirname(__DIR__) . '/common/start.php';

$request = array_merge($_GET, $_POST);

if (!isset($request['action']) || !$request['action']) {
    echo "What do you want?";
    return;
}

if (\App\Sign::verify($request) !== true) {
    echo 'no no no~';
    return;
}

echo \App\Sockets::run($request);
