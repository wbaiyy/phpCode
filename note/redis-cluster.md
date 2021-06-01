##redis-cluster

### 集群简介

Redis 集群是一个可以在多个 Redis 节点之间进行数据共享的设施（installation）。  
Redis 集群提供了以下两个好处：  

- 将数据自动切分（split）到多个节点的能力。  
- 当集群中的一部分节点失效或者无法进行通讯时， 仍然可以继续处理命令请求的能力。


###Redis 集群数据共享

Redis 集群使用数据分片（sharding）而非一致性哈希（consistency hashing）来实现： 一个 Redis 集群包含 16384 个哈希槽（hash slot）， 数据库中的每个键都属于这 16384 个哈希槽的其中一个， 集群使用公式 CRC16(key) % 16384 来计算键 key 属于哪个槽， 其中 CRC16(key) 语句用于计算键 key 的[CRC16 校验和](http://zh.wikipedia.org/wiki/%E5%BE%AA%E7%92%B0%E5%86%97%E9%A4%98%E6%A0%A1%E9%A9%97)  

优势：这种将哈希槽分布到不同节点的做法使得用户可以很容易地向集群中添加或者删除节点


### 创建并使用 Redis 集群

Redis 集群由多个运行在集群模式（cluster mode）下的 Redis 实例组成， 实例的集群模式需要通过配置来开启， 开启集群模式的实例将可以使用集群特有的功能和命令。   


下面是一个包含了最少选项的集群配置文件示例：

	port 7000  
	cluster-enabled yes             #开启集群模式
	cluster-config-file nodes.conf  #节点配置，默认指定名称，自动生成
	cluster-node-timeout 5000       #节点间超时时间，当集群出现分区，超过配置超时时间后，集群里多数一方，应该升级从节点；集群里少数一方，应该停止处理写命令， 并向客户端报告错误
	appendonly yes


要让集群正常运作至少需要三个主节点， 不过在刚开始试用集群功能时， 强烈建议使用六个节点： 其中三个为主节点， 而其余三个则是各个主节点的从节点。
	
	mkdir cluster-test
	cd cluster-test
	mkdir 7000 7001 7002 7003 7004 7005
	
	#分别启动各节点
	cd 7000
	../redis-server ./redis.conf
	...

	#创建集群. eg：redis-trib创建新集群， 检查集群， 或者对集群进行重新分片（reshared）等工作
	./redis-trib.rb create --replicas 1 127.0.0.1:7000 127.0.0.1:7001 \
	127.0.0.1:7002 127.0.0.1:7003 127.0.0.1:7004 127.0.0.1:7005	

	#或者通过redis-处理创建
	redis-cli  --cluster create --cluster-replicas 1 127.0.0.1:7001 127.0.0.1:7002 127.0.0.1:7003 127.0.0.1:7004 127.0.0.1:7005 127.0.0.1:7000