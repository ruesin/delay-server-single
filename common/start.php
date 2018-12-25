<?php

define('ROOT_DIR', dirname(__DIR__).'/');

//引入常量定义
require_once ROOT_DIR.'/config/constants.php';

//引入自动加载
require_once ROOT_DIR.'/vendor/autoload.php';

//初始化redis配置
\App\Utils\Config::init('redis');

//初始化私钥对
\App\Utils\Config::init('secrets');
