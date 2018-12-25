# delay-queue
基于 Redis 实现的延时队列，只提供简单的延时能力，不负责业务相关队列，需要在创建队列时提供业务队列的连接信息，消息到期时自动将消息移动到业务队列。

## 依赖
- Redis > 3.2
- PHP > 7.0
- [Swoole Extension](http://pecl.php.net/package/swoole) > 1.9.5
- [predis](https://github.com/nrk/predis) 1.1.1
- [swover](https://github.com/ruesin/swover) > 1.0.0

## 安装
```
$ composer create-project --prefer-dist ruesin/delay-server-single
```

## 配置
拷贝`./config/samples/*.php`到`./config/`，并按需修改：
- `constants.php` 常量定义
- `redis.php` Redis服务器配置
- `secrets.php` 验证签名的密钥对
- `server.php` 服务启动的配置文件，参考[swover](https://github.com/ruesin/swover)

## 使用
- Process 服务

  持续检查到期消息，将到期消息从延迟队列移动到活跃的业务队列。
```
$ php server.php process [start|stop|reload|restart]
```
- Sockets 服务

  接收客户端请求，实现创建队列、删除队列、发送延时消息。可以使用`http`或`tcp`启动相应的swoole服务。
```
$ php server.php [http|tcp] [start|stop|reload|restart]
```
也可以选择使用 nginx + php-fpm，将网站目录指向`./public/`即可。




