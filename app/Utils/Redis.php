<?php
namespace App\Utils;

/**
 * Redis
 * @see \Predis\Client
 */
class Redis
{
    private static $_instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return \Predis\ClientInterface
     */
    public static function instance($config = [])
    {
        $config = self::getConfig($config);

        $name = self::configToName($config);

        if (!isset(self::$_instance[$name])) {

            $parameters = [
                'host' => $config['host'],
                'port' => isset($config['port']) && $config['port'] ? $config['port'] : 6379,
            ];

            $options = [];
            if (isset($config['options'])) {
                $options = $config['options'];
            }

            if (isset($config['prefix'])) {
                $options['prefix'] = $config['prefix'];
            }

            if (isset($config['database'])) {
                $options['parameters']['database'] = $config['database'];
            }

            if (isset($config['password']) && $config['password']) {
                $options['parameters']['password'] = $config['password'];
            }

            self::$_instance[$name] = new \Predis\Client($parameters, $options);
        }

        return self::$_instance[$name];
    }

    public static function close($config = [])
    {
        $config = self::getConfig($config);
        $name   = self::configToName($config);

        if (isset(self::$_instance[$name])) {
            self::$_instance[$name]->quit();
            self::$_instance[$name] = null;
            unset(self::$_instance[$name]);
        }
        return true;
    }

    private static function getConfig($config)
    {
        if(!empty($config)) {
            return $config;
        }
        return Config::get('redis');
    }

    private static function configToName($config)
    {
        return md5(json_encode($config));
    }
}

