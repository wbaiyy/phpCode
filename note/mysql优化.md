# mysq优化步骤l
		
## 查找问题

- 通过 show status查看SQl执行的评率
	```
	show global status like "Com_%";  //全局统计
	show global status like "Innodb_%"; //innodb相关
	show global status like "Connections%"; //试图连接链接数
	show global status like "Slow_queries"; //慢日志条数
	show global status like "uptime"; //服务运行时间
	```
- 定位SQL效率低的SQL语句
	1. 开启慢日志查询，启动mysql时候配置，`--log-show-queries`
	2. 查看当前运行的语句和锁情况,`show processlist`,

- 通过explain语句查询低效语句执行计划

	1. 命中索引类型，性能越来越好，`type：all, index, range, ref, eq_ref, const/system, null`  
		```
		all：扫描全表
		index：扫描整个索引记录
		range：扫描索引记录范围
		ref：查询普通索引，单个值
		eq_ref：唯一索引或主键作为多表连接的条件
		const/system：通过主键或唯一索引，查询一条记录
		null：不扫描表和索引，如selec 1 from table;
		```
	2. 可能命中的索引：`possible_key` 
	3. 实际命中的索引：`key` 
	4. 查询扫描表的实际行数，数值越少，越快 ：`rows` 
	
- 通过show profile分析sql
	1. show profiles 查询编号
	2. show profile for query {no} 查询过程具体耗时

- 通过trace分析优化器选择执行计划

## 索引相关

### 存在索引但不能使用索引的场景

- 以%开头的的like语句

- 数据类型出现隐形转换的场景

- 组合索引，查询不符合最左原则

- 使用索引比全表扫描更慢的情况

- 使用`or` 语句, 但条件不全是索引

### 查看索引使用情况

- 执行命令 `show global status like "Handler_read%";`


	> Handler_read_key: 值越高说明， 使用索引频率高  
	> Handler_read_rnd_next: 值越高说明，扫描全表次数高
	
### SQL优化	

- 插入数据，采用批量插入代替单条插入

- order by优化
	```
	1.尽量通过索引排序，而非filesort 
	2.filesort优化，适当加大`max_length_for_sort_data` 和 `sort_buffer_size`
		max_length_for_sort_data: 值可以决定采用一次or两次扫描算法
		sort_buffer_size： 可以决定排序在内存还是磁盘
	```
- group by 优化
	1. group by field1，field2默认会 order by field1，field2，如不需要排序，可以使用 `order by null`



	
		
		
		
		
		
		
		
		
		
		
		
		