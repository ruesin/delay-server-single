<?php
namespace App;

use App\Utils\Queue;
use App\Utils\Redis;

class Handle
{
    private $queue_name = '';

    private $data = [];

    private $instance = null;

    public function __construct($request)
    {
        $this->queue_name = $request['queue_name'];

        if (isset($request['data'])) {
            $this->data = $request['data'];
        }

        $this->instance = Redis::instance();
    }

    /**
     * 创建队列
     *
     * @return string
     */
    public function createQueue()
    {
        if (!isset($this->data['delay_time']) || intval($this->data['delay_time']) <= 0) {
            return self::response(400, [], 'Param delay_time error!');
        }

        if (!isset($this->data['config']['host']) || !isset($this->data['config']['port']) || !isset($this->data['list_name'])) {
            return self::response(400, [], 'Param config error!');
        }

        $info = [
            'delay_time' => intval($this->data['delay_time']),
            'list_name'  => $this->data['list_name'],
            'config'     => $this->data['config']
        ];

        if ($this->instance->hsetnx(Queue::queueInfoName(), $this->queue_name, json_encode($info, JSON_UNESCAPED_UNICODE))) {
            $this->instance->rpush(Queue::queueListName(), $this->queue_name);
        } else {
            return self::response(400, [], 'Queue already exists!');
        }
        return self::response(200, [], 'Queue created successfully!');
    }

    /**
     * 删除队列
     * @return bool
     */
    public function dropQueue()
    {
        $this->instance->hdel(Queue::queueInfoName(), $this->queue_name);
        $this->instance->lrem(Queue::queueListName(), 0, $this->queue_name);

        $this->instance->del(Queue::delayName($this->queue_name));

        return self::response(200, [], 'Queue deleted successfully!');
    }

    /**
     * 发送消息
     * @return bool|string
     */
    public function addMessage()
    {
        if (!isset($this->data['message']) || !is_string($this->data['message'])) {
            return self::response(400, [], 'Message body error!');
        }

        $info = $this->instance->hget(Queue::queueInfoName(), $this->queue_name);
        if (!$info) {
            return self::response(400, [], 'Queue does not exist!');
        }
        $info = json_decode($info, true);

        if (isset($this->data['delay_time']) && $this->data['delay_time'] > 0) {
            $delay = $this->data['delay_time'];
        } else {
            $delay = $info['delay_time'];
        }

        if ($delay <= 0) {
            return self::response(400, [], 'Delay time less than 0!');
        }

        $this->instance->zadd(Queue::delayName($this->queue_name), time() + $delay, self::guuid($this->queue_name).$this->data['message']);

        return self::response(200, [], 'Message sent successfully!');
    }

    /**
     * @param int $status
     * @param array $data
     * @param string $message
     * @return false|string
     */
    public static function response($status = 200, $data = [], $message = 'success')
    {
        $result = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    private function guuid($salt)
    {
        return md5(uniqid(microtime(true).$salt.mt_rand(),true));
    }
}