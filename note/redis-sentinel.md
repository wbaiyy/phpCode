# redis sentinel

- 内部通信： `gossip`协议
- 选举算法： `Raft` 算法

## 用途

- 监控（Monitoring）： Sentinel 会不断地检查你的主服务器和从服务器是否运作正常。
- 提醒（Notification）：当被监控的某个 Redis 服务器出现问题时， Sentinel 可以通过 API 向管理员或者其他应用程序发送通知。
- 自动故障迁移（Automatic failover）： 当一个主服务器不能正常工作时， Sentinel 会开始一次自动故障迁移操作,通过raft算法选举一个从服务器作为主服务（slave no one）， 并其他从服务器改为复制新的主服务器； 当客户端试图连接失效的主服务器时，集群也会向客户端返回新主服务器的地址。

## 启动方式
- redis-sentinel /path/to/sentinel.conf
- redis-server /path/to/sentinel.conf --sentinel
    ```
	#最少配置文件
	sentinel monitor mymaster 127.0.0.1 6379 2     
	sentinel down-after-milliseconds mymaster 60000  //Sentinel 认为服务器已经断线所需的毫秒数
	sentinel failover-timeout mymaster 180000  //执行故障迁移超时时间
	sentinel parallel-syncs mymaster 1   //在执行故障转移时，最多可以有多少个从服务器同时对新的主服务器进行同步
	```
	
	第一行配置： Sentinel 去监视一个名为 mymaster 的主服务器，这个主服务器的 IP 地址为 127.0.0.1 ， 端口号为 6379 ， 而将这个主服务器判断为失效至少需要 2 个 Sentinel 同意。
	
## 每个Sentinel定时任务
	- 每个 Sentinel 以每秒钟一次的频率向它所知的主服务器、从服务器以及其他 Sentinel 实例发送一个 PING 命令。
	- 在一般情况下， 每个 Sentinel 会以每 10 秒一次的频率向它已知的所有主服务器和从服务器发送 INFO [section] 命令。 当一个主服务器被 Sentinel 标记为客观下线时， Sentinel 向下线主服务器的所有从服务器发送 INFO [section] 命令的频率会从 10 秒一次改为每秒一次。

## 自动发现 Sentinel 和从服务器
	-  Sentinel 可以通过发布与订阅功能来自动发现正在监视相同主服务器的其他 Sentinel ， 这一功能是通过向频道 `__sentinel__:hello` 发送信息来实现的。
	