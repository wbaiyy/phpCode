##redis-streams

### 新增Stream 
- 命令：`xadd`：
- 示例： `xadd streamName * name wang age 10 `  

	>   其中 * 为服务器自动生成ID，格式int-int，后面为hash格式数据  
	

	
### 查看streams的长度
- 命令： `xlen`
- 示例：`xlen streanName`
	> 返回当前stream的长度

### 根据ID范围查看数据
- 命令： `xrange`  
- 示例： `xrange streamName - +` , `xrange streamName 0-0 1622102182364-0`
		
###  独立消费数据 类似list模式
- 命令：`xread`
- 示例： `xread count 2 streams streamName 0-0`
	>从streamName里面的0-0ID后两条数据

###  创建消费组  
- 命令：`xgroup`
- 示例：  
	//表示从头开始消费  
	`xgroup create streamName cg1 0-0`   

	//表示从尾部开始消费，只接受新消息，当前 Stream 消息会全部忽略  
	 xgroup create streamName cg2 $


### 消费组消费
- 命令： `xreadgroup`
- 示例：`xreadgroup GROUP cg1 c1 count 1 streams streamName >` ，`xreadgroup GROUP cg1 c1 count 1 streams streamName 0-0` 

	> 表示创建或直接使用cg1分组从streamName里面消费1条数据，其中 >表示从分组 last_delivered_id 后面开始读，
	> 也可以通过ID指定消息位置


### 查看stream的分组信息
- 命令： `xinfo groups`
- 示例： `xinfo groups streamName`
	>1) 1) "name"  
    >   2) "comsume1"  
        3) "consumers"  
        4) (integer) 2  
        5) "pending"  
        6) (integer) 8  
        7) "last-delivered-id"  
        8) "1622169729286-0"  
     2) 1) "name"  
        2) "comsume2"  
        3) "consumers"  
        4) (integer) 0  
        5) "pending"  
        6) (integer) 0  
        7) "last-delivered-id"  
        8) "0-0"  

### 查看stream分组内消费者信息
- 命令：  `xinfo consumers`
- 示例： `xinfo consumers streamName cg1`
	>   1) 1) "name"  
		   2) "c1"  
		   3) "pending"    
		   4) (integer) 5    
		   5) "idle"  
		   6) (integer) 81927  
		2) 1) "name"  
		   2) "c2"  
		   3) "pending"  
		   4) (integer) 3  
		   5) "idle"  
		   6) (integer) 212977  

### 分组内消费者ack某一条消息
- 命令：`xack`
- 示例：`xack streamName cg1 1527851486781-0` 


### 其他命令
-  查看stream的信息：`xinfo stream streamName` 

-  删除某stream中的消息：`xdel streamName {id}` ,其中id如1622169729286-0
	