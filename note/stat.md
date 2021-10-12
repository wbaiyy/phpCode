## 思拓stat

> 统计类型参数  ，主要包括如下字段：  
> `action`:  行为动作， 包括 `pv`，`like`, `share`,`comment`   
> `type`: 客户端类型， 包括 `app`, `wap`, `pc`   
> `sid`: 站点ID，目前默认为10001   
> `aid`:  未使用到  
> `cid`:  内容ID
> `ip`:  客户端IP
> `have_cooike`:  值为0时并且type为app时，下面的cookie值才能是UV  
> `cooike`:值为空时并且action为pv，表示uv（type为APP时候，还需要上面have_cooike为零） 


### 当天被访问过的内容集合  

- key: SerAna:dconid:{day}:{siteId} 例：`SerAna:dconid:0520:10001`
- 类型：集合
- 有效期：当天0点+ 2*24小时  
- 示例 ： 
	
		localhost:6379> smembers SerAna:dconid:0520:10001  
		 1) "0"  
		 2) "1"  
		 3) "10"  
		 4) "18"  
		 5) "221599"  
		 6) "222610"  
		 7) "393813"  
		 8) "394857"  
		 9) "395110"  
		10) "395174"  
		11) "395181"  
		12) "395428"  
		13) "395790"  
		14) "395798"  
		15) "395819"  
		...... 


###  当天站点数据详情

- key: SerAna:site:{day}:{siteId} 例: `SerAna:site:0521:10001`
- 类型: Hash 
- 有效期：当天0点+ 2*24小时  
- 示例：  

		localhost:6379> hgetall SerAna:site:0521:10001    
		 1) "pv_wap"  
		 2) "63"
		 3) "ip_wap"  
		 4) "38"
		 5) "uv_wap"  
		 6) "31"
		 7) "pv_app"  
		 8) "107"
		 9) "ip_app"  
		10) "24"
		11) "uv_app"  
		12) "65"
		13) "shares_app"  
		14) "11"
		15) "pv_pc"  
		16) "12"
		17) "ip_pc"  
		18) "3"
		19) "uv_pc"  
		20) "1"
		21) "likes_app"  
		22) "1"

###  当天单个内容数据详情统计

- key: SerAna:dcon:{day}:{siteId}:{contentId} 例：`SerAna:dcon:0521:10001:421045`
- 类型: Hash 
- 有效期：当天0点+ 2*24小时  
- 示例：  

		localhost:6379> hgetall SerAna:dcon:0521:10001:421045  
		1) "pv_wap"  
		2) "8"  
		3) "click_pv"  
		4) "153"  
		5) "ip_wap"  
		6) "1"  
		7) "uv_wap"  
		8) "2"  
		9) "pv_app"  
		10) "6"  
		11) "ip_app"  
		12) "1"  
		13) "uv"  
		14) "5"  


### 单个内容数据全部统计

- key: SerAna:con:{siteId}:{contentId} 例: `SerAna:con:10001:426603`
- 类型: Hash 
- 有效期：当天0点+ 2*24小时  
- 示例：  

		localhost:6379> hgetall SerAna:dcon:0521:10001:421045  
		 1) "pv"  
		 2) "36"  
		 3) "click_pv"  
		 4) "391"  
		 5) "shares"  
		 6) "4"  
		 7) "likes"  
		 8) "0"  
		 9) "comments"  
		10) "0"  
		11) "pv_app"  
		12) "16"  
		13) "pv_wap"  
		14) "20"  
		15) "shares_app"  
		16) "4"  


### 各个端ip统计

- key: SerAna:ipset:{siteId}:{type} 例: `SMEMBERS SerAna:ipset:10001:app` 其中app可以换成 wap和pc
- 类型: 集合 
- 有效期：当天0点+ 24小时  
- 示例：  

		localhost:6379> SMEMBERS SerAna:ipset:10001:app  
		 1) "172.20.205.198"  
		 2) "14.26.71.157"  
		 3) "172.20.109.191"  
		 4) ""  
		 5) "172.20.201.162"  
		 6) "172.20.205.42"
		 7) "fe80::c0c3:6cff:febb:71c2%dummy0"  
		 8) "192.168.31.88"  
		 9) "192.168.31.39"  
		10) "192.168.31.163"  
		11) "202.104.129.54"  
		12) "116.24.100.224"  
		13) "116.24.100.67"  
		14) "10.3.179.92"  
		15) "27.38.254.27"  
		16) "14.21.43.103"  


### 当天内容ip统计

- key: SerAna:ip:{day}:{siteId}:{contentId} 例: `SerAna:ip:0521:10001:421045` 
- 类型: 集合 
- 有效期：24小时  
- 示例：  

		localhost:6379> SMEMBERS SerAna:ip:0521:10001:421045    
		 1) "172.20.205.198"  
		 2) "14.26.71.157"  
		 3) "172.20.109.191"  
		 4) ""  
		 


### 统计每篇内容的uv

- key: SerAna:cookie:{day}:{siteId}:{contentId} 例: `SerAna:cookie:0521:10001:421045` 
- 类型: 集合 
- 有效期：24小时  
- 示例：  

		localhost:6379> SMEMBERS SerAna:cookie:0521:10001:421045       
		 1) "PHPSESSID=mk7e3g5cearbujk7cf0dui5f84; tj_id=wKgKr2CnYKMT/xa9AwNTAg=="   
		 2) "tj_id=wKgKr2CnUqAUOBa+AwNfAg==; Users_CtmediaSetting=%7B%22id%22%3A%221%22%2C%22siteid%22%3A%2210001%22%2C%22platform_name%22%3A%22test%22%2C%22platform_thumb%22%3A%22https%3A%5C%2F%5C%2Fimg.dutenews.com%5C%2Fa%5C%2F10001%5C%2F201802%5C%2F1da525916f145eeb5f24328a1795fdcc.jpg%3F20180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620180112140829%2620190112140829%22%2C%22fans_num%22%3A%220%22%2C%22subscribe_num%22%3A%220%22%2C%22is_pv%22%3A%221%22%2C%22isday_num%22%3A%220%22%2C%22day_num%22%3A%2210%22%2C%22agreement_title%22%3A%22%5Cu6df1%5Cu5733%5Cu53f7%5Cu5165%5Cu9a7b%5Cu534f%5Cu8bae%22%2C%22tips%22%3A%22%5Cu672c%5Cu6587%5Cu7531%5Cu3010%5Cu8bfb%5Cu7279%5Cu3011%5Cu6df1%5Cu5733%5Cu53f7%5Cu5165%5Cu9a7b%5Cu5355%5Cu4f4d%5Cu53d1%5Cu5e03%5Cuff0c%5Cu4e0d%5Cu4ee3%5Cu8868%5Cu3010%5Cu8bfb%5Cu7279%5Cu3011%5Cu7684%5Cu89c2%5Cu70b9%5Cu548c%5Cu7acb%5Cu573a%5Cu3002%5Cu5982%5Cu6709%5Cu4fb5%5Cu6743%5Cuff0c%5Cu8bf7%5Cu8054%5Cu7cfb%5Cu6211%5Cu4eec%5Cu6838%5Cu5b9e%5Cu540e%5Cu5220%5Cu9664%5Cuff01%5Cu8054%5Cu7cfb%5Cu90ae%5Cu7bb1%5Cuff1adutenews%40163.com%5Cuff0c%5Cu5165%5Cu9a7b%5Cu54a8%5Cu8be2%5Cu7535%5Cu8bdd%5Cuff1a0755-83518822%22%2C%22istips%22%3A%221%22%7D"


 


 