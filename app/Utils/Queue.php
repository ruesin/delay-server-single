<?php
namespace App\Utils;

use Swover\Utils\Config;

class Queue
{
    public static function queueListName()
    {
        return Config::get('queue.list_name');
    }

    public static function queueInfoName()
    {
        return Config::get('queue.info_name');
    }

    public static function delayName($queue_name)
    {
        return $queue_name.Config::get('queue.delay_suffix');
    }
}