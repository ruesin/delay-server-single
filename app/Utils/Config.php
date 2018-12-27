<?php
namespace App\Utils;

class Config
{
    private static $config = [];

    public static function init(string $path)
    {
        $dp = dir($path);
        while ($file = $dp ->read()){
            if($file !="." && $file !=".." && is_file($path.$file) && strrchr($file,'.') == '.php'){
                try {
                    $content = include $path.$file;
                    if (is_array($content) || is_string($content)) {
                        self::$config[str_replace('.php','',$file)] = $content;
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Init config fail!');
                }
            }
        }
        $dp->close();
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        if (empty(self::$config)) {
            return $default;
        }

        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        $keys = explode('.', $key);
        $result = self::$config;
        foreach ($keys as $k) {
            if (isset($result[$k])) {
                $result = $result[$k];
                if (!is_array($result)) {
                    break;
                }
            } else {
                return $default;
            }
        }
        return $result;
    }
}