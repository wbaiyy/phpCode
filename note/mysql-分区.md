## mysql分区

### 分区概念

> 将表的数据根据一定规则，分解成多个独立的对象进行存储。

### 分区优势

- 分散存储可以存储更多的数据
- 优化查询：在有where条件的查询中，可以只查询一个分区和多个分区，不需要查询全表数据，从而提高查询效率
- 优化聚合函数：涉及到sum、count这类聚合函数时，可以在分区上并行处理，最终汇总所有分区的结果
- 快速删除： 对于在分区上面过期的或不需要的数据，可以通过删除分区来做到快速删除数据
- 提高查询吞吐量: 可以通过分散分区到不同磁盘，

### 分区类型

- range
	> 通过对patition key的字段范围进行分区
	```
	create table 表名(
	 .........
	)engine=MyISAM partition by range(字段)( 
		partition p0 values less then(10000),
		partition p1 values less then(20000),
		partition p2 values less then(30000)
		....
	);
	```
	
- list
	> 通过对patition key的字段枚举类型进行分区
		```
	create table 表名(
	 .........
	)engine=MyISAM partition by list(字段)( 
		partition p0 values in(1,2),
		partition p1 values in(3,4),
		partition p2 values in(5,6)
		....
	);
	```
	
- hash和linear hash
	> hash通过取模的hash算法，linear hash通过一个线性的2的幂运算法则；同时支持表达式和直接使用字段；
	> 但必须都是整数
	
	```
	create table 表名(
	 .........
	)engine=MyISAM partition by hash(算法函数)) partitions 分区的数量;
	
	
	create table student(
	  id mediumint unsigned auto_increment not null,
	  birthday date,
	  primary key (id,birthday) 
	)engine=MyISAM partition by hash(month(birthday)) partitions 12;
	
	解释:
	month()提取日期中的月份
	hash(month(birthday))   按照日期中的月份进行分区
	```
	
- key和linear key
	> 和hash类似，都是hash算法，但是key支持取模的算法，但是支持类型除text和blob，其余均支持；
	
	```
	create table tableName (
      字段.......   
	)engine=表引擎 partition by key (id) partitions num;
	```


