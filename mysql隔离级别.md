# Mysql隔离级别

## 隔离级别类型

- 读未提交（Read Uncommitted）  
- 读已提交（Read Committed）
- 可重复读（Repeatable Read）
- 串行化（Serializable）

## MySql隔离级别的实现原理
实现隔离机制的方法主要有两种：  
- 读写锁  
- 一致性快照读，即 MVCC   

MySql使用不同的锁策略(Locking Strategy)/MVCC来实现四种不同的隔离级别。RR、RC的实现原理跟MVCC有关，RU和Serializable跟锁有关         


1. 读未提交（Read Uncommitted）     

读未提交，采取的是读不加锁原理。  
    - 事务读不加锁，不阻塞其他事务的读和写；     
    - 事务写阻塞其他事务写，但不阻塞其他事务读；    

所以会出现脏读、不可重复读和幻读问题

2. 读已提交（Read Committed）

通过mvcc机制解决了脏读问题，由于在同一个事物中每次读都会生成新版本号，所以解决不了不可重复读   
在当前隔离级别下，只会加记录琐，不会加间隙琐，所以会出现幻读情况

3. 可重复读（Repeatable Read）

通过mvcc机制解决了脏读问题，由于在同一个事物中只会生成唯一版本号，解决不可重复读问题    
在当前隔离级别下，只会加记录琐+间隙琐，形成next-key琐，解决幻读情况

4. 串行化（Serializable）

在读写模式下均会添加对应的读写琐，所以不会出现现脏读、不可重复读和幻读问题
   
## 参考:  
- [一文彻底读懂MySQL事务的四大隔离级别](https://mp.weixin.qq.com/s?__biz=Mzg3NzU5NTIwNg==&mid=2247487976&idx=1&sn=083dbec7efe85961adbd84656d1e6ac5&source=41#wechat_redirect)
- [Innodb中的事务隔离级别和锁的关系](https://tech.meituan.com/2014/08/20/innodb-lock.html)