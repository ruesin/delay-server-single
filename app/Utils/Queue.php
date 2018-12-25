<?php
namespace App\Utils;

class Queue
{
    public static function queueListName()
    {
        return QUEUE_LIST;
    }

    public static function queueInfoName()
    {
        return QUEUE_INFO;
    }

    public static function delayName($queue_name)
    {
        return $queue_name.DELAY_SUFFIX;
    }
}