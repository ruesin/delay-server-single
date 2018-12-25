<?php
namespace App;

use App\Utils\Queue;
use App\Utils\Redis;

class Process
{
    /**
     * @var \Predis\ClientInterface
     */
    private static $redisInstance = null;

    public static function run()
    {
        self::$redisInstance = Redis::instance();

        $name = self::$redisInstance->lpop(Queue::queueListName());
        if (!$name) {
            sleep(1);
            return true;
        }

        $info = self::$redisInstance->hget(Queue::queueInfoName(), $name);
        if (!$info) {
            sleep(1);
            return true;
        }
        $info = json_decode($info, true);

        $score = time();
        self::delayToActive($name, $score, $info);

        if ( self::$redisInstance->hexists(Queue::queueInfoName(), $name) ) {
            self::$redisInstance->rpush(Queue::queueListName(), $name);
        }

        sleep(1);
        return true;
    }

    private static function delayToActive($name, $score, $info)
    {
        $delays = self::$redisInstance->zrangebyscore(Queue::delayName($name), 0, $score);

        if (empty($delays)) {
            return true;
        }

        $activeInstance  = Redis::instance($info['config']);
        foreach ($delays as $value) {
            $activeInstance->rpush($info['list_name'], substr($value, 32));
        }

        self::$redisInstance->zremrangebyscore(Queue::delayName($name), 0, $score);
        return true;
    }
}