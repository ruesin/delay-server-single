<?php
namespace App\Utils;

class Config
{
    private static $config = [];

    public static function init(string $key)
    {
        $file = ROOT_DIR.'/config/'.$key.'.php';
        if (file_exists($file)) {
            self::$config[$key] = include $file;
        }
    }

    public static function get(string $key, string $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }
}